<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingCancellation;
use App\Models\Payment;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CancelMarketplaceBooking
{
    public function __construct(private readonly ProductNotificationService $notifications, private readonly BookingLifecycleService $lifecycle) {}

    public function handle(User $actor, Booking $booking, ?string $reason = null, string $requesterType = 'guest'): BookingCancellation
    {
        return DB::transaction(function () use ($actor, $booking, $reason, $requesterType) {
            $query = Booking::with(['guest', 'property.marketplaceListing', 'business'])->lockForUpdate();
            if ($requesterType === 'guest') {
                $query->where('guest_user_id', $actor->id);
            } else {
                $query->where('business_id', $booking->business_id);
            }

            $booking = $query->findOrFail($booking->id);
            if ($existing = $booking->cancellations()->where('status', 'completed')->first()) {
                return $existing;
            }

            if (! in_array($booking->status->value, ['reserved', 'awaiting_payment', 'confirmed'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Only an upcoming booking can be cancelled.',
                ]);
            }

            $checkInTime = $booking->property->marketplaceListing?->check_in_time ?: '14:00';
            $timezone = $booking->business->timezone ?: 'Africa/Lagos';
            $checkIn = CarbonImmutable::parse($booking->arrival_date->toDateString().' '.$checkInTime, $timezone);
            $refundable = $requesterType === 'owner' || now()->lt($checkIn->subHours(48));
            $original = $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->first();
            $refund = null;
            if ($refundable && $original) {
                $refund = Payment::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'original_payment_id' => $original->id, 'reference' => 'REF-'.$original->reference, 'purpose' => 'refund', 'amount' => $original->amount, 'currency' => $original->currency, 'method' => $original->method, 'provider' => $original->provider, 'provider_reference' => 'SIM-REF-'.($original->provider_reference ?: $booking->id), 'status' => 'completed', 'transaction_at' => now(), 'verified_by' => $actor->id, 'verified_at' => now(), 'provider_metadata' => ['simulated' => true, 'initiated_by' => $requesterType], 'created_by' => $actor->id]);
            }

            $refundAmount = $refund ? (float) $refund->amount : 0;
            $cancellation = BookingCancellation::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'requested_by' => $actor->id, 'requester_type' => $requesterType, 'reason' => $reason, 'policy_snapshot' => ['free_cancellation_hours' => 48, 'refundable' => $refundable, 'initiated_by' => $requesterType], 'refund_amount' => $refundAmount, 'cancellation_fee' => $refundable ? 0 : $booking->total_amount, 'currency' => $booking->currency, 'refund_payment_id' => $refund?->id, 'approved_by' => $actor->id, 'requested_at' => now(), 'approved_at' => now(), 'completed_at' => now(), 'status' => 'completed']);
            $booking->availabilityDays()->where('active_key', 'active')->update(['active_key' => null, 'released_at' => now(), 'release_reason' => $requesterType.'_cancellation', 'status' => 'released']);
            $booking->operationalTasks()->whereNotIn('status', ['completed', 'cancelled'])->update([
                'status' => 'cancelled', 'updated_by' => $actor->id, 'updated_at' => now(),
            ]);
            $booking = $this->lifecycle->cancel($booking, $actor, $requesterType, $reason);
            $booking->update(['payment_status' => $refundable ? 'refunded' : $booking->payment_status]);

            $refundMessage = $refund ? 'A full simulated refund was recorded.' : ($refundable ? 'No captured payment required a refund.' : 'The booking was inside the 48-hour cancellation window, so no refund was recorded.');
            $this->notifications->user($booking->guest, $booking->business, 'booking_cancelled', 'Booking cancelled', $refundMessage, ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $ownerMessage = $requesterType === 'guest'
                ? "{$booking->guest->name} cancelled {$booking->reference} for {$booking->property->name}."
                : "{$actor->name} cancelled {$booking->reference} for {$booking->property->name}.";
            $this->notifications->businessOwners($booking->business, 'booking_cancelled', 'Booking cancelled', $ownerMessage, ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);

            return $cancellation;
        });
    }
}
