<?php

namespace App\Services\Calendar;

use App\Enums\BookingStatus;
use App\Models\Business;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class OwnerCalendarWorkspace
{
    /** @return array<string, mixed> */
    public function month(Business $business, CarbonImmutable $month, ?string $propertyId = null): array
    {
        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();
        $gridStart = $monthStart->startOfWeek(CarbonImmutable::SUNDAY);
        $gridEnd = $monthEnd->endOfWeek(CarbonImmutable::SATURDAY);
        $properties = $business->properties()->orderBy('name')->get(['id', 'name']);
        $selectedProperty = $propertyId && $properties->contains('id', $propertyId) ? $propertyId : null;
        $blockingStatuses = collect(BookingStatus::cases())->filter->blocksAvailability()->map->value->all();

        $bookings = $business->bookings()->with(['property', 'guest'])
            ->whereIn('status', $blockingStatuses)
            ->where('payment_status', '!=', 'failed')
            ->when($selectedProperty, fn ($query, $id) => $query->where('property_id', $id))
            ->whereDate('arrival_date', '<=', $gridEnd)
            ->whereDate('departure_date', '>', $gridStart)
            ->get();

        $blocks = $business->properties()->when($selectedProperty, fn ($query, $id) => $query->whereKey($id))
            ->with(['availabilityBlocks' => fn ($query) => $query
                ->where('status', 'active')->where('block_state', 'active')->where('blocks_booking', true)
                ->whereDate('starts_on', '<=', $gridEnd)->whereDate('ends_on', '>', $gridStart)])
            ->get()->flatMap->availabilityBlocks;

        $days = collect(range(0, $gridStart->diffInDays($gridEnd)))->map(function (int $offset) use ($gridStart, $monthStart, $bookings, $blocks): array {
            $date = $gridStart->addDays($offset);
            $events = new Collection;
            foreach ($bookings as $booking) {
                if ($date->gte($booking->arrival_date) && $date->lt($booking->departure_date)) {
                    $events->push(['type' => 'booking', 'label' => $booking->reference, 'property' => $booking->property->name, 'booking_id' => $booking->id, 'source' => $booking->source->value]);
                }
            }
            foreach ($blocks as $block) {
                if ($date->gte($block->starts_on) && $date->lt($block->ends_on)) {
                    $events->push(['type' => 'block', 'label' => $block->reason ?: 'Blocked', 'property' => $block->property->name, 'block_id' => $block->id]);
                }
            }

            return ['date' => $date, 'in_month' => $date->month === $monthStart->month, 'events' => $events];
        });

        return compact('monthStart', 'properties', 'selectedProperty', 'days', 'bookings', 'blocks') + [
            'previousMonth' => $monthStart->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->addMonth()->format('Y-m'),
        ];
    }
}
