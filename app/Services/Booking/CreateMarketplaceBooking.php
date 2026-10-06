<?php

namespace App\Services\Booking;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyAvailabilityDay;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use App\Services\Operations\BookingOperationsService;
use App\Services\Platform\PlatformSettings;
use App\Enums\IdentityVerificationStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateMarketplaceBooking
{
    public function __construct(private PropertyAvailabilityService $availability, private BookingPricingService $pricing, private PaymentGateway $gateway, private BookingOperationsService $operations, private ProductNotificationService $notifications, private BookingLifecycleService $lifecycle, private PlatformSettings $settings) {}

    public function handle(User $guest, Property $property, array $data): Booking
    {
        $property->load('business');
        if ($property->business->status->value !== 'active') {
            throw ValidationException::withMessages(['property' => 'This property is not accepting new bookings at the moment.']);
        }
        if ($guest->businessMemberships()->where('business_id', $property->business_id)->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['property' => 'You cannot book a property that belongs to your own business workspace.']);
        }
        if ($guest->identity_verification_status !== IdentityVerificationStatus::Verified) {
            throw ValidationException::withMessages(['identity' => 'Please complete NIN verification before making payment for a stay.']);
        }

        if ($old = Booking::where('guest_user_id', $guest->id)->where('external_reference', $data['idempotency_key'])->first()) {
            return $old;
        }

        [$booking, $successful] = DB::transaction(function () use ($guest, $property, $data): array {
            $property = Property::with('business')->lockForUpdate()->findOrFail($property->id);
            if ($property->business->status->value !== 'active') {
                throw ValidationException::withMessages(['property' => 'This property is not accepting new bookings at the moment.']);
            }
            if ($guest->businessMemberships()->where('business_id', $property->business_id)->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['property' => 'You cannot book a property that belongs to your own business workspace.']);
            }
            if ($guest->identity_verification_status !== IdentityVerificationStatus::Verified) {
                throw ValidationException::withMessages(['identity' => 'Please complete NIN verification before making payment for a stay.']);
            }
            $arrival = CarbonImmutable::parse($data['arrival_date']);
            $departure = CarbonImmutable::parse($data['departure_date']);
            $guests = (int) $data['adult_count'] + (int) ($data['child_count'] ?? 0);
            if ($guests > $property->capacity) {
                throw ValidationException::withMessages(['adult_count' => 'Guest count exceeds this apartment capacity.']);
            }if (! $this->availability->isAvailable($property, $arrival, $departure)) {
                throw ValidationException::withMessages(['arrival_date' => 'These dates are no longer available.']);
            }$quote = $this->pricing->quote($property, $arrival, $departure);
            if (abs((float) $data['quoted_total'] - (float) $quote->decimal($quote->totalMinor)) > 0.009) {
                throw ValidationException::withMessages(['quoted_total' => 'The price changed before confirmation. Please review the updated total and confirm again.']);
            }
            $reference = 'VS-'.strtoupper(Str::random(10));
            $paymentOption = $data['payment_option'] ?? 'full';
            $installmentsEnabled = (bool) $this->settings->get('payments.installments_enabled', true);
            if ($paymentOption === 'installment' && ! $installmentsEnabled) {
                throw ValidationException::withMessages(['payment_option' => 'Installment payment is not currently enabled.']);
            }
            $depositPercentage = max(1, min(99, (int) $this->settings->get('payments.deposit_percentage', 50)));
            $totalAmount = (float) $quote->decimal($quote->totalMinor);
            $minimumDepositAmount = round($totalAmount * ($depositPercentage / 100), 2);
            $requestedDepositAmount = isset($data['deposit_amount']) ? round((float) $data['deposit_amount'], 2) : null;
            if ($paymentOption === 'installment' && $requestedDepositAmount !== null && $requestedDepositAmount < $minimumDepositAmount) {
                throw ValidationException::withMessages(['deposit_amount' => 'Deposit amount cannot be less than the required minimum of ₦'.number_format($minimumDepositAmount).'.']);
            }
            if ($paymentOption === 'installment' && $requestedDepositAmount !== null && $requestedDepositAmount > $totalAmount) {
                throw ValidationException::withMessages(['deposit_amount' => 'Deposit amount cannot be greater than the booking total.']);
            }
            $chargeAmount = $paymentOption === 'installment' ? ($requestedDepositAmount ?: $minimumDepositAmount) : $totalAmount;
            $chargeMinor = (int) round($chargeAmount * 100);
            $balanceDueAt = $arrival->subHours((int) $this->settings->get('payments.balance_due_hours_before_checkin', 24));
            $paymentPlan = $paymentOption === 'installment' ? [
                'type' => 'installment',
                'deposit_percentage' => $depositPercentage,
                'minimum_deposit_amount' => $minimumDepositAmount,
                'deposit_amount' => $chargeAmount,
                'balance_amount' => round($totalAmount - $chargeAmount, 2),
                'balance_due_at' => $balanceDueAt->toIso8601String(),
                'must_be_fully_paid_before_checkin' => true,
            ] : ['type' => 'full', 'must_be_fully_paid_before_checkin' => true];
            $booking = Booking::create(['business_id' => $property->business_id, 'property_id' => $property->id, 'guest_user_id' => $guest->id, 'reference' => $reference, 'arrival_date' => $arrival, 'departure_date' => $departure, 'number_of_guests' => $guests, 'adult_count' => $data['adult_count'], 'child_count' => $data['child_count'] ?? 0, 'source' => 'marketplace', 'status' => 'awaiting_payment', 'payment_status' => 'unpaid', 'currency' => $quote->currency, 'subtotal_amount' => $quote->decimal($quote->subtotalMinor), 'discount_amount' => $quote->decimal($quote->discountMinor), 'total_amount' => $quote->decimal($quote->totalMinor), 'external_reference' => $data['idempotency_key'], 'special_requests' => $data['special_requests'] ?? null, 'source_metadata' => ['payment_provider' => $data['payment_provider'] ?? 'paystack'], 'payment_plan' => $paymentPlan, 'payment_due_at' => $paymentOption === 'installment' ? $balanceDueAt : $arrival, 'check_in_instructions_snapshot' => $property->check_in_instructions, 'created_by' => $guest->id]);
            if (blank($guest->phone_number)) {
                $guest->update(['phone_number' => $data['guest_phone']]);
            }
            BookingGuest::query()->create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'user_id' => $guest->id, 'guest_type' => 'adult', 'is_primary' => true, 'full_name' => $guest->name, 'email' => $guest->email, 'phone_number' => $data['guest_phone'], 'identity_verification_status' => $guest->identity_verification_status->value, 'status' => 'active', 'created_by' => $guest->id, 'updated_by' => $guest->id]);
            BookingStatusHistory::create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'previous_status' => null, 'new_status' => 'awaiting_payment', 'source' => 'marketplace', 'reason' => 'Checkout created', 'occurred_at' => now(), 'status' => 'active']);
            $result = $this->gateway->charge($reference, $chargeMinor, $quote->currency);
            Payment::create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'reference' => 'PAY-'.$reference, 'purpose' => $paymentOption === 'installment' ? 'deposit' : 'balance', 'amount' => $chargeAmount, 'currency' => $quote->currency, 'method' => 'card', 'provider' => $data['payment_provider'] ?? 'paystack', 'provider_reference' => $result->providerReference, 'status' => $result->successful ? 'completed' : 'failed', 'transaction_at' => now(), 'verified_by' => $guest->id, 'verified_at' => now(), 'provider_metadata' => $result->metadata + ['payment_option' => $paymentOption], 'created_by' => $guest->id]);
            if (! $result->successful) {
                $booking->update(['payment_status' => 'failed']);

                return [$booking->fresh(), false];
            }
            $booking->update(['payment_status' => $paymentOption === 'installment' ? 'partially_paid' : 'paid']);
            $booking = $this->lifecycle->confirm($booking, $guest, 'payment', $paymentOption === 'installment' ? 'Simulated deposit payment completed' : 'Simulated payment completed');
            for ($date = $arrival; $date->lt($departure); $date = $date->addDay()) {
                PropertyAvailabilityDay::create(['business_id' => $property->business_id, 'property_id' => $property->id, 'booking_id' => $booking->id, 'availability_date' => $date, 'availability_state' => 'booked', 'source_type' => 'marketplace', 'source_reference' => $reference, 'active_key' => 'active', 'allocated_at' => now(), 'status' => 'active', 'created_by' => $guest->id, 'updated_by' => $guest->id]);
            }
            $this->operations->onBookingConfirmed($booking, $guest);
            $business = $property->business;
            $paymentCopy = $paymentOption === 'installment' ? " Deposit received; remaining balance is due before check-in." : '';
            $this->notifications->user($guest, $business, 'booking_confirmed', 'Your stay is confirmed', "{$property->name} is booked from {$arrival->format('d M Y')} to {$departure->format('d M Y')}.{$paymentCopy}", ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $this->notifications->businessOwners($business, 'new_booking', 'New marketplace booking', "{$guest->name} booked {$property->name} for {$guests} guests. Payment status: ".str_replace('_', ' ', $booking->payment_status->value).'.', ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);

            return [$booking->fresh(), true];
        });

        if (! $successful) {
            throw ValidationException::withMessages(['payment' => 'Payment was not successful.']);
        }

        return $booking;
    }
}
