<?php

namespace App\Services\Calendar;

use App\Enums\BookingStatus;
use App\Models\Business;
use App\Models\ExternalCalendarConnection;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class OwnerCalendarWorkspace
{
    /** @return array<string, mixed> */
    public function month(Business $business, CarbonImmutable $month, ?string $propertyId = null, ?array $propertyIds = null): array
    {
        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();
        $gridStart = $monthStart->startOfWeek(CarbonImmutable::SUNDAY);
        $gridEnd = $monthEnd->endOfWeek(CarbonImmutable::SATURDAY);
        $properties = $business->properties()->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds))->orderBy('name')->get(['id', 'name']);
        $selectedProperty = $propertyId && $properties->contains('id', $propertyId) ? $propertyId : null;
        $blockingStatuses = collect(BookingStatus::cases())->filter->blocksAvailability()->map->value->all();
        $calendarStatuses = [...$blockingStatuses, BookingStatus::CheckedOut->value, BookingStatus::Completed->value];

        $bookings = $business->bookings()->with(['property', 'guest'])
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->whereIn('status', $calendarStatuses)
            ->where('payment_status', '!=', 'failed')
            ->when($selectedProperty, fn ($query, $id) => $query->where('property_id', $id))
            ->whereDate('arrival_date', '<=', $gridEnd)
            ->whereDate('departure_date', '>', $gridStart)
            ->get();

        $blocks = $business->properties()->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds))->when($selectedProperty, fn ($query, $id) => $query->whereKey($id))
            ->with(['availabilityBlocks' => fn ($query) => $query
                ->where('status', 'active')->where('block_state', 'active')->where('blocks_booking', true)
                ->whereDate('starts_on', '<=', $gridEnd)->whereDate('ends_on', '>', $gridStart)])
            ->get()->flatMap->availabilityBlocks;

        $tasks = $business->operationalTasks()->with('property')
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->whereNotIn('status', ['completed', 'cancelled'])->whereNotNull('due_at')
            ->when($selectedProperty, fn ($query, $id) => $query->where('property_id', $id))
            ->whereBetween('due_at', [$gridStart->startOfDay(), $gridEnd->endOfDay()])->get();
        $connections = ExternalCalendarConnection::query()->where('business_id', $business->id)->where('status', 'active')
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->when($selectedProperty, fn ($query, $id) => $query->where('property_id', $id))->get();
        $calendarHealth = [
            'connections' => $connections->count(),
            'attention' => $connections->filter(fn ($connection) => $connection->sync_status === 'failed' || $connection->consecutive_failure_count > 0 || ! $connection->last_synced_at || $connection->last_synced_at->lt(now()->subMinutes(45)))->count(),
        ];

        $days = collect(range(0, $gridStart->diffInDays($gridEnd)))->map(function (int $offset) use ($gridStart, $monthStart, $bookings, $blocks, $tasks): array {
            $date = $gridStart->addDays($offset);
            $events = new Collection;
            foreach ($blocks as $block) {
                if ($date->gte($block->starts_on) && $date->lt($block->ends_on)) {
                    $rangeStart = CarbonImmutable::instance($block->starts_on);
                    $rangeEnd = CarbonImmutable::instance($block->ends_on);
                    $events->push([
                        'type' => 'block',
                        'label' => $block->reason ?: 'Blocked',
                        'property' => $block->property->name,
                        'block_id' => $block->id,
                        'starts_on' => $rangeStart->toDateString(),
                        'ends_on' => $rangeEnd->toDateString(),
                        'range_label' => $rangeStart->format('j M').' – '.$rangeEnd->format('j M Y').' · available again '.$rangeEnd->format('j M'),
                        'segment_start' => $date->isSameDay($rangeStart) || $date->dayOfWeek === CarbonImmutable::SUNDAY,
                        'segment_end' => $date->addDay()->gte($rangeEnd) || $date->dayOfWeek === CarbonImmutable::SATURDAY,
                    ]);
                }
            }
            foreach ($bookings as $booking) {
                if ($date->gte($booking->arrival_date) && $date->lt($booking->departure_date)) {
                    $rangeStart = CarbonImmutable::instance($booking->arrival_date);
                    $rangeEnd = CarbonImmutable::instance($booking->departure_date);
                    $events->push([
                        'type' => 'booking',
                        'label' => $booking->reference,
                        'property' => $booking->property->name,
                        'booking_id' => $booking->id,
                        'source' => $booking->source->value,
                        'status' => $booking->status->value,
                        'blocks_availability' => $booking->status->blocksAvailability(),
                        'starts_on' => $rangeStart->toDateString(),
                        'ends_on' => $rangeEnd->toDateString(),
                        'range_label' => $rangeStart->format('j M').' – '.$rangeEnd->format('j M Y'),
                        'segment_start' => $date->isSameDay($rangeStart) || $date->dayOfWeek === CarbonImmutable::SUNDAY,
                        'segment_end' => $date->addDay()->gte($rangeEnd) || $date->dayOfWeek === CarbonImmutable::SATURDAY,
                    ]);
                }
            }
            foreach ($tasks as $task) {
                if ($task->due_at->isSameDay($date)) {
                    $events->push(['type' => 'task', 'label' => $task->title, 'property' => $task->property->name, 'task_id' => $task->id]);
                }
            }

            return ['date' => $date, 'in_month' => $date->month === $monthStart->month, 'events' => $events];
        });

        return compact('monthStart', 'properties', 'selectedProperty', 'days', 'bookings', 'blocks', 'tasks', 'connections', 'calendarHealth') + [
            'previousMonth' => $monthStart->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->addMonth()->format('Y-m'),
        ];
    }
}
