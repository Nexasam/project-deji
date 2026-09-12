<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingCancellation;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class CancelMarketplaceBooking
{
    public function handle(User $guest, Booking $booking, ?string $reason = null): BookingCancellation
    {
        return DB::transaction(function () use ($guest, $booking, $reason) {
            $booking = Booking::where('guest_user_id', $guest->id)->lockForUpdate()->findOrFail($booking->id);
            if ($existing = $booking->cancellations()->where('status', 'completed')->first()) {
                return $existing;
            }$checkIn = CarbonImmutable::parse($booking->arrival_date->toDateString().' 14:00', 'Africa/Lagos');
            $refundable = now()->lt($checkIn->subHours(48));
            $original = $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->first();
            $refund = null;
            if ($refundable && $original) {
                $refund = Payment::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'original_payment_id' => $original->id, 'reference' => 'REF-'.$original->reference, 'purpose' => 'refund', 'amount' => $original->amount, 'currency' => $original->currency, 'method' => $original->method, 'provider' => $original->provider, 'provider_reference' => 'SIM-REF-'.$original->provider_reference, 'status' => 'completed', 'transaction_at' => now(), 'verified_by' => $guest->id, 'verified_at' => now(), 'provider_metadata' => ['simulated' => true], 'created_by' => $guest->id]);
            }$cancellation = BookingCancellation::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'requested_by' => $guest->id, 'requester_type' => 'guest', 'reason' => $reason, 'policy_snapshot' => ['free_cancellation_hours' => 48, 'refundable' => $refundable], 'refund_amount' => $refundable ? $booking->total_amount : 0, 'cancellation_fee' => $refundable ? 0 : $booking->total_amount, 'currency' => $booking->currency, 'refund_payment_id' => $refund?->id, 'approved_by' => $guest->id, 'requested_at' => now(), 'approved_at' => now(), 'completed_at' => now(), 'status' => 'completed']);
            $booking->availabilityDays()->where('active_key', 'active')->update(['active_key' => null, 'released_at' => now(), 'release_reason' => 'guest_cancellation', 'status' => 'released']);
            $previousStatus = $booking->status->value;
            $booking->update(['status' => 'cancelled', 'payment_status' => $refundable ? 'refunded' : $booking->payment_status]);
            BookingStatusHistory::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'previous_status' => $previousStatus, 'new_status' => 'cancelled', 'source' => 'guest', 'reason' => $reason ?: 'Guest cancellation', 'occurred_at' => now(), 'status' => 'active']);

            return $cancellation;
        });
    }
}
