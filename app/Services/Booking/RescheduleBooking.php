<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingDateChange;
use App\Models\PropertyAvailabilityDay;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RescheduleBooking
{
    public function __construct(private readonly PropertyAvailabilityService $availability) {}

    /** @param array{arrival_date: string, departure_date: string, reason: string} $data */
    public function handle(Booking $booking, User $owner, array $data): BookingDateChange
    {
        return DB::transaction(function () use ($booking, $owner, $data): BookingDateChange {
            $booking = Booking::query()->with('property')->lockForUpdate()->findOrFail($booking->id);
            if (! in_array($booking->status->value, ['reserved', 'awaiting_payment', 'confirmed'], true)) {
                throw ValidationException::withMessages(['arrival_date' => 'This booking can no longer be rescheduled.']);
            }

            $arrival = CarbonImmutable::parse($data['arrival_date']);
            $departure = CarbonImmutable::parse($data['departure_date']);
            if (! $this->availability->isAvailable($booking->property, $arrival, $departure, $booking)) {
                throw ValidationException::withMessages(['arrival_date' => 'These dates conflict with an existing booking or availability block.']);
            }

            $previousArrival = $booking->arrival_date->toDateString();
            $previousDeparture = $booking->departure_date->toDateString();
            $booking->availabilityDays()->where('active_key', 'active')->update([
                'active_key' => null, 'released_at' => now(),
                'release_reason' => 'booking_rescheduled', 'status' => 'released', 'updated_by' => $owner->id,
            ]);

            for ($date = $arrival; $date->lt($departure); $date = $date->addDay()) {
                PropertyAvailabilityDay::query()->create([
                    'business_id' => $booking->business_id, 'property_id' => $booking->property_id,
                    'booking_id' => $booking->id, 'availability_date' => $date->toDateString(),
                    'availability_state' => 'booked', 'source_type' => 'owner_adjustment',
                    'source_reference' => $booking->reference, 'active_key' => 'active',
                    'allocated_at' => now(), 'status' => 'active',
                    'created_by' => $owner->id, 'updated_by' => $owner->id,
                ]);
            }

            $booking->update(['arrival_date' => $arrival, 'departure_date' => $departure]);

            return BookingDateChange::query()->create([
                'business_id' => $booking->business_id, 'booking_id' => $booking->id,
                'previous_arrival_date' => $previousArrival, 'previous_departure_date' => $previousDeparture,
                'new_arrival_date' => $arrival->toDateString(), 'new_departure_date' => $departure->toDateString(),
                'change_type' => 'reschedule', 'availability_status' => 'allocated',
                'reason' => $data['reason'], 'approved_by' => $owner->id,
                'approved_at' => now(), 'occurred_at' => now(), 'status' => 'active',
            ]);
        });
    }
}
