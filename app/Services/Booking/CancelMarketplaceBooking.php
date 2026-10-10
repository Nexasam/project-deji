<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingCancellation;
use App\Models\Payment;
use App\Models\RefundRequest;
use App\Models\User;
use App\Jobs\InitiateProviderRefund;
use App\Services\Notifications\ProductNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Platform\PlatformSettings;

final class CancelMarketplaceBooking
{
    public function __construct(private readonly ProductNotificationService $notifications, private readonly BookingLifecycleService $lifecycle, private readonly PlatformSettings $settings) {}

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
            $freeCancellationHours = max(0, (int) $this->settings->get('bookings.free_cancellation_hours', 48));
            $refundable = $requesterType === 'owner' || now()->lt($checkIn->subHours($freeCancellationHours));
            $captured = $booking->payments()->where('status', 'completed')->where('purpose', '!=', 'refund')->orderBy('transaction_at')->get();
            $capturedTotal = (float) $captured->sum('amount');
            $isPartPayment = $capturedTotal + 0.0001 < (float) $booking->total_amount;
            $penaltyRate = $refundable && $requesterType === 'guest' && $isPartPayment
                ? max(0, min(100, (float) $this->settings->get('payments.partial_refund_penalty_percentage', 10))) : 0;
            $refunds = collect();
            $refundRequests = collect();
            if ($refundable) {
                foreach ($captured as $original) {
                    $approvedAmount = round((float) $original->amount * (1 - ($penaltyRate / 100)), 2);
                    if ($approvedAmount <= 0) continue;
                    $refund = Payment::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'original_payment_id' => $original->id, 'reference' => 'REF-'.$original->reference, 'purpose' => 'refund', 'amount' => $approvedAmount, 'currency' => $original->currency, 'method' => $original->method, 'provider' => $original->provider, 'provider_reference' => null, 'status' => 'pending', 'transaction_at' => now(), 'provider_metadata' => ['initiated_by' => $requesterType, 'penalty_percentage' => $penaltyRate], 'created_by' => $actor->id]);
                    $refunds->push($refund);
                    $refundRequests->push(RefundRequest::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'original_payment_id' => $original->id, 'refund_payment_id' => $refund->id, 'reference' => 'RR-'.$original->reference, 'reason_type' => 'booking_cancellation', 'reason' => $reason, 'requested_amount' => $original->amount, 'approved_amount' => $approvedAmount, 'currency' => $original->currency, 'requested_by' => $actor->id, 'reviewed_by' => $actor->id, 'approved_by' => $actor->id, 'requested_at' => now(), 'reviewed_at' => now(), 'approved_at' => now(), 'refund_status' => 'approved', 'created_by' => $actor->id, 'updated_by' => $actor->id]));
                }
            }

            $refundAmount = (float) $refunds->sum('amount');
            $cancellationFee = $refundable ? round($capturedTotal - $refundAmount, 2) : $capturedTotal;
            $cancellation = BookingCancellation::create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'requested_by' => $actor->id, 'requester_type' => $requesterType, 'reason' => $reason, 'policy_snapshot' => ['free_cancellation_hours' => $freeCancellationHours, 'refundable' => $refundable, 'initiated_by' => $requesterType, 'part_payment' => $isPartPayment, 'penalty_percentage' => $penaltyRate], 'refund_amount' => $refundAmount, 'cancellation_fee' => $cancellationFee, 'currency' => $booking->currency, 'refund_payment_id' => $refunds->first()?->id, 'approved_by' => $actor->id, 'requested_at' => now(), 'approved_at' => now(), 'completed_at' => now(), 'status' => 'completed']);
            $booking->availabilityDays()->where('active_key', 'active')->update(['active_key' => null, 'released_at' => now(), 'release_reason' => $requesterType.'_cancellation', 'status' => 'released']);
            $booking->operationalTasks()->whereNotIn('status', ['completed', 'cancelled'])->update([
                'status' => 'cancelled', 'updated_by' => $actor->id, 'updated_at' => now(),
            ]);
            $booking = $this->lifecycle->cancel($booking, $actor, $requesterType, $reason);
            $refundRequests->each(fn (RefundRequest $refundRequest) => InitiateProviderRefund::dispatch($refundRequest->id)->afterCommit());

            $refundMessage = $refunds->isNotEmpty() ? 'Your refund request was accepted and is being processed by the payment provider.' : ($refundable ? 'No captured payment required a refund.' : 'The booking was inside the cancellation window, so no refund was recorded.');
            $this->notifications->user($booking->guest, $booking->business, 'booking_cancelled', 'Booking cancelled', $refundMessage, ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $ownerMessage = $requesterType === 'guest'
                ? "{$booking->guest->name} cancelled {$booking->reference} for {$booking->property->name}."
                : "{$actor->name} cancelled {$booking->reference} for {$booking->property->name}.";
            $this->notifications->businessOwners($booking->business, 'booking_cancelled', 'Booking cancelled', $ownerMessage, ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);

            return $cancellation;
        });
    }
}
