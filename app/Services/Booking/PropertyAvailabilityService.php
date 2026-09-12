<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Property;
use Carbon\CarbonImmutable;

final class PropertyAvailabilityService
{
    public function isAvailable(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure, ?Booking $ignoreBooking = null): bool
    {
        if ($departure->lessThanOrEqualTo($arrival)) {
            return false;
        }

        return ! $property->bookings()->when($ignoreBooking, fn ($query) => $query->whereKeyNot($ignoreBooking->id))
            ->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed', 'checked_in'])
            ->where('payment_status', '!=', 'failed')
            ->whereDate('arrival_date', '<', $departure->toDateString())
            ->whereDate('departure_date', '>', $arrival->toDateString())->exists()
            && ! $property->availabilityDays()->when($ignoreBooking, fn ($query) => $query->where(function ($query) use ($ignoreBooking): void {
                $query->whereNull('booking_id')->orWhere('booking_id', '!=', $ignoreBooking->id);
            }))
                ->whereIn('availability_state', ['held', 'booked', 'blocked'])
                ->whereDate('availability_date', '>=', $arrival->toDateString())
                ->whereDate('availability_date', '<', $departure->toDateString())->exists()
            && ! $property->availabilityBlocks()->where('status', 'active')
                ->where('block_state', 'active')->where('blocks_booking', true)
                ->whereDate('starts_on', '<', $departure->toDateString())
                ->whereDate('ends_on', '>', $arrival->toDateString())->exists();
    }
}
