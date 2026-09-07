<?php

namespace App\Services\Booking;

use App\Models\Property;
use Carbon\CarbonImmutable;

final class PropertyAvailabilityService
{
    public function isAvailable(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure): bool
    {
        if ($departure->lessThanOrEqualTo($arrival)) {
            return false;
        }

        return ! $property->bookings()->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed', 'checked_in'])
            ->whereDate('arrival_date', '<', $departure->toDateString())
            ->whereDate('departure_date', '>', $arrival->toDateString())->exists()
            && ! $property->availabilityDays()->where('active_key', 'active')
                ->whereIn('availability_state', ['held', 'booked', 'blocked'])
                ->whereDate('availability_date', '>=', $arrival->toDateString())
                ->whereDate('availability_date', '<', $departure->toDateString())->exists();
    }
}
