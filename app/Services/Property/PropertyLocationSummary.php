<?php

namespace App\Services\Property;

use App\Enums\BookingStatus;
use App\Models\Business;
use App\Models\Property;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class PropertyLocationSummary
{
    /**
     * @param  Collection<int, Property>  $properties
     * @return array{all: array{property_count: int, occupancy: int}, locations: array<int, array{name: string, manager: string, property_count: int, occupancy: int}>}
     */
    public function build(Business $business, Collection $properties): array
    {
        $timezone = $business->timezone ?: config('app.timezone');
        $periodStart = CarbonImmutable::now($timezone)->startOfMonth();
        $periodEnd = $periodStart->addMonth();
        $propertyIds = $properties->pluck('id');
        $bookedNights = $propertyIds->isEmpty()
            ? collect()
            : $business->bookings()
                ->whereIn('property_id', $propertyIds)
                ->whereIn('status', collect(BookingStatus::cases())->filter->blocksAvailability()->map->value)
                ->where('arrival_date', '<', $periodEnd->toDateString())
                ->where('departure_date', '>', $periodStart->toDateString())
                ->get(['property_id', 'arrival_date', 'departure_date'])
                ->groupBy('property_id')
                ->map(fn (Collection $bookings): int => $bookings->sum(function ($booking) use ($periodStart, $periodEnd): int {
                    $arrival = CarbonImmutable::instance($booking->arrival_date)->startOfDay();
                    $departure = CarbonImmutable::instance($booking->departure_date)->startOfDay();
                    $start = $arrival->greaterThan($periodStart) ? $arrival : $periodStart;
                    $end = $departure->lessThan($periodEnd) ? $departure : $periodEnd;

                    return max(0, $start->diffInDays($end, false));
                }));

        $daysInMonth = $periodStart->daysInMonth;
        $locationGroups = $properties->groupBy(fn ($property): string => trim((string) (data_get($property->address, 'city') ?: data_get($property->address, 'state') ?: 'Location not set')));
        $locations = $locationGroups->map(function (Collection $locationProperties, string $location) use ($bookedNights, $daysInMonth): array {
            $managerNames = $locationProperties
                ->flatMap->staffAssignments
                ->map(fn ($assignment) => $assignment->employee?->businessMembership?->user?->name)
                ->filter()
                ->unique()
                ->values();

            return [
                'name' => $location,
                'manager' => $managerNames->count() > 1 ? $managerNames->count().' assigned managers' : ($managerNames->first() ?: 'Manager not assigned'),
                'property_count' => $locationProperties->count(),
                'occupancy' => $this->occupancy($locationProperties->pluck('id'), $bookedNights, $daysInMonth),
            ];
        })->sortByDesc('property_count')->values()->all();

        return [
            'all' => [
                'property_count' => $properties->count(),
                'occupancy' => $this->occupancy($propertyIds, $bookedNights, $daysInMonth),
            ],
            'locations' => $locations,
        ];
    }

    /** @param Collection<int, string> $propertyIds */
    private function occupancy(Collection $propertyIds, Collection $bookedNights, int $daysInMonth): int
    {
        $availableNights = $propertyIds->count() * $daysInMonth;

        if ($availableNights === 0) {
            return 0;
        }

        $occupiedNights = $propertyIds->sum(fn (string $propertyId): int => (int) $bookedNights->get($propertyId, 0));

        return (int) round(($occupiedNights / $availableNights) * 100);
    }
}
