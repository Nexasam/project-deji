<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingCheckIn;
use App\Models\BookingCheckOut;
use App\Models\BookingStatusHistory;
use App\Models\DomainEvent;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use App\Services\Operations\BookingOperationsService;
use App\Services\Reviews\VerifiedStayReviewService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class BookingLifecycleService
{
    public function __construct(
        private readonly ProductNotificationService $notifications,
        private readonly VerifiedStayReviewService $reviews,
    ) {}

    public function confirm(Booking $booking, User $actor, string $source = 'payment', ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $source, $reason): Booking {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['awaiting_payment'], 'Only a booking awaiting payment can be confirmed.');
            $this->recordTransition($booking, 'confirmed', $actor, $reason ?: 'Payment or owner confirmation completed', $source);

            return $booking->fresh();
        });
    }

    public function cancel(Booking $booking, User $actor, string $source, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $source, $reason): Booking {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['reserved', 'awaiting_payment', 'confirmed'], 'Only an upcoming booking can be cancelled.');
            $this->recordTransition($booking, 'cancelled', $actor, $reason ?: str($source)->title().' cancellation', $source);

            return $booking->fresh();
        });
    }

    public function checkIn(Booking $booking, User $actor, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $data): Booking {
            $booking = Booking::query()->with(['property', 'business', 'guest'])->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['confirmed'], 'Only a confirmed booking can be checked in.');

            BookingCheckIn::query()->updateOrCreate(['booking_id' => $booking->id], [
                'business_id' => $booking->business_id,
                'identity_status' => $data['identity_status'],
                'balance_status' => $data['balance_status'],
                'security_deposit_status' => $data['security_deposit_status'] ?? 'not_required',
                'access_method' => $data['access_method'] ?? null,
                'access_reference' => $data['access_reference'] ?? null,
                'checklist' => $data['checklist'] ?? null,
                'blocking_issues' => [],
                'completed_by' => $actor->id,
                'completed_at' => now(),
                'status' => 'completed',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);
            $this->recordTransition($booking, 'checked_in', $actor, $data['notes'] ?? 'Guest checked in');
            $booking->property->update(['operational_status' => 'occupied', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);
            $this->notifyState($booking, 'checked_in', 'Guest checked in');

            return $booking->fresh(['checkIn']);
        });
    }

    public function checkOut(Booking $booking, User $actor, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $data): Booking {
            $booking = Booking::query()->with(['property', 'business', 'guest'])->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['checked_in'], 'Only a checked-in booking can be checked out.');

            BookingCheckOut::query()->updateOrCreate(['booking_id' => $booking->id], [
                'business_id' => $booking->business_id,
                'actual_departure_at' => now(),
                'access_return_status' => $data['access_return_status'] ?? 'returned',
                'room_condition' => $data['room_condition'],
                'damage_status' => $data['damage_status'] ?? 'none',
                'damage_notes' => $data['damage_notes'] ?? null,
                'deposit_release_status' => $data['deposit_release_status'] ?? 'not_required',
                'handover_notes' => $data['handover_notes'] ?? null,
                'completed_by' => $actor->id,
                'completed_at' => now(),
                'status' => 'completed',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);
            $this->recordTransition($booking, 'checked_out', $actor, $data['handover_notes'] ?? 'Guest checked out');
            app(BookingOperationsService::class)->onBookingCheckedOut($booking->fresh(), $actor);
            $this->notifyState($booking, 'checked_out', 'Guest checked out');

            return $booking->fresh(['checkOut']);
        });
    }

    public function markNoShow(Booking $booking, User $actor, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $reason): Booking {
            $booking = Booking::query()->with(['property.marketplaceListing', 'business', 'guest'])->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['confirmed'], 'Only a confirmed booking can be marked as no-show.');
            $checkInTime = $booking->property->marketplaceListing?->check_in_time ?: '14:00';
            $timezone = $booking->business->timezone ?: 'Africa/Lagos';
            $expectedArrival = CarbonImmutable::parse($booking->arrival_date->toDateString().' '.$checkInTime, $timezone);
            if (now($timezone)->lt($expectedArrival)) {
                throw ValidationException::withMessages(['status' => 'This guest cannot be marked as no-show before the scheduled check-in time.']);
            }
            $booking->availabilityDays()->where('active_key', 'active')->update([
                'active_key' => null, 'released_at' => now(), 'release_reason' => 'booking_no_show',
                'status' => 'released', 'updated_by' => $actor->id,
            ]);
            $this->recordTransition($booking, 'no_show', $actor, $reason ?: 'Guest did not arrive');
            $booking->property->update(['operational_status' => 'available', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);
            $this->notifyState($booking, 'no_show', 'Booking marked as no-show');

            return $booking->fresh();
        });
    }

    public function complete(Booking $booking, User $actor, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $reason): Booking {
            $booking = Booking::query()->with(['property', 'business', 'guest'])->lockForUpdate()->findOrFail($booking->id);
            $this->requireState($booking, ['checked_out'], 'Only a checked-out booking can be completed.');
            $this->recordTransition($booking, 'completed', $actor, $reason ?: 'Stay operations completed');
            $booking->property->update(['operational_status' => 'available', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);
            $this->notifyState($booking, 'completed', 'Stay completed');
            $this->reviews->invite($booking, $actor);

            return $booking->fresh();
        });
    }

    private function requireState(Booking $booking, array $allowed, string $message): void
    {
        if (! in_array($booking->status->value, $allowed, true)) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    private function recordTransition(Booking $booking, string $to, User $actor, string $reason, string $source = 'owner'): void
    {
        $from = $booking->status->value;
        $booking->update(['status' => $to]);
        BookingStatusHistory::query()->create([
            'business_id' => $booking->business_id, 'booking_id' => $booking->id,
            'previous_status' => $from, 'new_status' => $to, 'source' => $source,
            'reason' => $reason, 'metadata' => ['actor_id' => $actor->id],
            'occurred_at' => now(), 'status' => 'active',
        ]);
        DomainEvent::query()->create([
            'business_id' => $booking->business_id, 'property_id' => $booking->property_id,
            'booking_id' => $booking->id, 'event_name' => "booking.{$to}",
            'aggregate_type' => Booking::class, 'aggregate_id' => $booking->id,
            'idempotency_key' => "booking:{$booking->id}:{$to}",
            'payload' => ['from' => $from, 'to' => $to, 'actor_id' => $actor->id],
            'occurred_at' => now(), 'publication_status' => 'pending', 'status' => 'active',
        ]);
    }

    private function notifyState(Booking $booking, string $state, string $title): void
    {
        $label = str($state)->replace('_', ' ')->toString();
        $this->notifications->user($booking->guest, $booking->business, "booking_{$state}", $title,
            "Booking {$booking->reference} for {$booking->property->name} is now {$label}.", ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
        $this->notifications->businessOwners($booking->business, "booking_{$state}", $title,
            "Booking {$booking->reference} for {$booking->property->name} is now {$label}.", ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);
    }
}
