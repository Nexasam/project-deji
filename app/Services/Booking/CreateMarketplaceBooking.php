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
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateMarketplaceBooking
{
    public function __construct(private PropertyAvailabilityService $availability, private BookingPricingService $pricing, private PaymentGateway $gateway, private BookingOperationsService $operations, private ProductNotificationService $notifications, private BookingLifecycleService $lifecycle) {}

    public function handle(User $guest, Property $property, array $data): Booking
    {
        $property->load('business');
        if ($property->business->status->value !== 'active') {
            throw ValidationException::withMessages(['property' => 'This property is not accepting new bookings at the moment.']);
        }

        if ($old = Booking::where('guest_user_id', $guest->id)->where('external_reference', $data['idempotency_key'])->first()) {
            return $old;
        }

        [$booking, $successful] = DB::transaction(function () use ($guest, $property, $data): array {
            $property = Property::with('business')->lockForUpdate()->findOrFail($property->id);
            if ($property->business->status->value !== 'active') {
                throw ValidationException::withMessages(['property' => 'This property is not accepting new bookings at the moment.']);
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
            $booking = Booking::create(['business_id' => $property->business_id, 'property_id' => $property->id, 'guest_user_id' => $guest->id, 'reference' => $reference, 'arrival_date' => $arrival, 'departure_date' => $departure, 'number_of_guests' => $guests, 'adult_count' => $data['adult_count'], 'child_count' => $data['child_count'] ?? 0, 'source' => 'marketplace', 'status' => 'awaiting_payment', 'payment_status' => 'unpaid', 'currency' => $quote->currency, 'subtotal_amount' => $quote->decimal($quote->subtotalMinor), 'discount_amount' => $quote->decimal($quote->discountMinor), 'total_amount' => $quote->decimal($quote->totalMinor), 'external_reference' => $data['idempotency_key'], 'special_requests' => $data['special_requests'] ?? null, 'created_by' => $guest->id]);
            if (blank($guest->phone_number)) {
                $guest->update(['phone_number' => $data['guest_phone']]);
            }
            BookingGuest::query()->create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'user_id' => $guest->id, 'guest_type' => 'adult', 'is_primary' => true, 'full_name' => $guest->name, 'email' => $guest->email, 'phone_number' => $data['guest_phone'], 'identity_verification_status' => 'unverified', 'status' => 'active', 'created_by' => $guest->id, 'updated_by' => $guest->id]);
            BookingStatusHistory::create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'previous_status' => null, 'new_status' => 'awaiting_payment', 'source' => 'marketplace', 'reason' => 'Checkout created', 'occurred_at' => now(), 'status' => 'active']);
            $result = $this->gateway->charge($reference, $quote->totalMinor, $quote->currency);
            Payment::create(['business_id' => $property->business_id, 'booking_id' => $booking->id, 'reference' => 'PAY-'.$reference, 'purpose' => 'balance', 'amount' => $quote->decimal($quote->totalMinor), 'currency' => $quote->currency, 'method' => 'card', 'provider' => 'paystack', 'provider_reference' => $result->providerReference, 'status' => $result->successful ? 'completed' : 'failed', 'transaction_at' => now(), 'verified_by' => $guest->id, 'verified_at' => now(), 'provider_metadata' => $result->metadata, 'created_by' => $guest->id]);
            if (! $result->successful) {
                $booking->update(['payment_status' => 'failed']);

                return [$booking->fresh(), false];
            }
            $booking->update(['payment_status' => 'paid']);
            $booking = $this->lifecycle->confirm($booking, $guest, 'payment', 'Simulated payment completed');
            for ($date = $arrival; $date->lt($departure); $date = $date->addDay()) {
                PropertyAvailabilityDay::create(['business_id' => $property->business_id, 'property_id' => $property->id, 'booking_id' => $booking->id, 'availability_date' => $date, 'availability_state' => 'booked', 'source_type' => 'marketplace', 'source_reference' => $reference, 'active_key' => 'active', 'allocated_at' => now(), 'status' => 'active', 'created_by' => $guest->id, 'updated_by' => $guest->id]);
            }
            $this->operations->onBookingConfirmed($booking, $guest);
            $business = $property->business;
            $this->notifications->user($guest, $business, 'booking_confirmed', 'Your stay is confirmed', "{$property->name} is booked from {$arrival->format('d M Y')} to {$departure->format('d M Y')}.", ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $this->notifications->businessOwners($business, 'new_booking', 'New marketplace booking', "{$guest->name} booked {$property->name} for {$guests} guests.", ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);

            return [$booking->fresh(), true];
        });

        if (! $successful) {
            throw ValidationException::withMessages(['payment' => 'Payment was not successful.']);
        }

        return $booking;
    }
}
