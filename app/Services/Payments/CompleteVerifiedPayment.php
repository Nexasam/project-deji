<?php

namespace App\Services\Payments;

use App\Data\VerifiedPayment;
use App\Models\BookingFinancialAllocation;
use App\Models\Payment;
use App\Models\PropertyAvailabilityDay;
use App\Services\Booking\BookingLifecycleService;
use App\Services\Notifications\ProductNotificationService;
use App\Services\Operations\BookingOperationsService;
use App\Services\Platform\PlatformSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class CompleteVerifiedPayment
{
    public function __construct(
        private readonly BookingLifecycleService $lifecycle,
        private readonly BookingOperationsService $operations,
        private readonly ProductNotificationService $notifications,
        private readonly PlatformSettings $settings,
    ) {}

    public function handle(VerifiedPayment $verified): Payment
    {
        return DB::transaction(function () use ($verified): Payment {
            $payment = Payment::query()->with(['booking.guest', 'booking.property', 'booking.business'])
                ->where('provider', $verified->provider)
                ->where(function ($query) use ($verified): void {
                    $query->where('provider_reference', $verified->reference)->orWhere('reference', $verified->reference);
                })->lockForUpdate()->firstOrFail();

            if ($payment->status->value === 'completed') {
                return $payment;
            }

            if ((int) round(((float) $payment->amount) * 100) !== $verified->amountMinor || strtoupper($payment->currency) !== $verified->currency) {
                throw new RuntimeException('The verified payment amount or currency does not match the booking payment.');
            }

            $payment->forceFill([
                'status' => 'completed',
                'provider_reference' => $verified->reference,
                'verified_at' => now(),
                'transaction_at' => now(),
                'provider_metadata' => array_merge($payment->provider_metadata ?? [], [
                    'provider_transaction_id' => $verified->providerTransactionId,
                    'verification' => $verified->metadata,
                ]),
            ])->save();

            $booking = $payment->booking;
            $paid = (float) $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->sum('amount');
            $fullyPaid = $paid + 0.0001 >= (float) $booking->total_amount;
            $booking->forceFill(['payment_status' => $fullyPaid ? 'paid' : 'partially_paid'])->save();

            $this->recordAllocations($payment);

            if ($booking->status->value === 'awaiting_payment') {
                $booking = $this->lifecycle->confirm($booking, $booking->guest, 'payment', 'Provider payment verified');
                for ($date = CarbonImmutable::parse($booking->arrival_date); $date->lt(CarbonImmutable::parse($booking->departure_date)); $date = $date->addDay()) {
                    PropertyAvailabilityDay::query()->firstOrCreate([
                        'business_id' => $booking->business_id,
                        'property_id' => $booking->property_id,
                        'booking_id' => $booking->id,
                        'availability_date' => $date,
                        'active_key' => 'active',
                    ], [
                        'availability_state' => 'booked', 'source_type' => 'marketplace',
                        'source_reference' => $booking->reference, 'allocated_at' => now(),
                        'status' => 'active', 'created_by' => $booking->guest_user_id, 'updated_by' => $booking->guest_user_id,
                    ]);
                }
                $this->operations->onBookingConfirmed($booking, $booking->guest);
            }

            $message = $fullyPaid ? 'Your payment is complete and your arrival guide is available.' : 'Your part payment was received. The remaining balance is due before check-in.';
            $this->notifications->user($booking->guest, $booking->business, 'booking_payment_received', 'Payment received', $message, ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $this->notifications->businessOwners($booking->business, 'booking_payment_received', 'Booking payment received', "Payment was received for {$booking->reference}.", ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);
            $this->notifications->platformAdmins($booking->business, 'platform_payment_received', 'Platform payment received', "{$verified->currency} ".number_format($verified->amountMinor / 100, 2)." was received for {$booking->reference} via ".ucfirst($verified->provider).'.', ['booking_id' => $booking->id]);

            return $payment->refresh();
        });
    }

    private function recordAllocations(Payment $payment): void
    {
        if (BookingFinancialAllocation::query()->where('payment_id', $payment->id)->exists()) {
            return;
        }
        $booking = $payment->booking;
        $snapshot = $booking->pricing_policy_snapshot ?? [];
        $ratio = (float) $booking->total_amount > 0 ? min(1, (float) $payment->amount / (float) $booking->total_amount) : 0;
        $netStay = max(0, (float) $booking->subtotal_amount - (float) $booking->discount_amount);
        $feeTotal = data_get($snapshot, 'fee_payer') === 'guest'
            ? (float) $booking->service_fee_amount
            : round($netStay * ((float) data_get($snapshot, 'platform_fee_percentage', 5) / 100) + (float) data_get($snapshot, 'platform_fee_fixed', 0), 2);
        $fee = round($feeTotal * $ratio, 2);
        $tax = round((float) $booking->tax_amount * $ratio, 2);
        $owner = round(max(0, (float) $payment->amount - $fee - $tax), 2);
        foreach ([['platform_fee', 'credit', $fee, 'Verified Shortlet platform fee'], ['tax_liability', 'credit', $tax, 'Tax collected for the property operator'], ['owner_balance', 'credit', $owner, 'Amount due to property operator']] as [$type, $direction, $amount, $description]) {
            if ($amount <= 0) continue;
            BookingFinancialAllocation::query()->create(['business_id' => $payment->business_id, 'booking_id' => $payment->booking_id, 'payment_id' => $payment->id, 'allocation_type' => $type, 'direction' => $direction, 'amount' => $amount, 'currency' => $payment->currency, 'recognized_on' => now()->toDateString(), 'description' => $description, 'status' => 'active']);
        }
    }
}
