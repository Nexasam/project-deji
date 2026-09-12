<?php

namespace App\Services\Calendar;

use App\Enums\BookingPaymentStatus;
use App\Models\Property;

final class BuildPropertyCalendarFeed
{
    public function build(Property $property): string
    {
        $events = [];
        foreach ($property->bookings()->get() as $booking) {
            if (! $booking->status->blocksAvailability() || $booking->payment_status === BookingPaymentStatus::Failed) continue;
            $events[] = $this->event('booking-'.$booking->id, $booking->arrival_date->format('Ymd'), $booking->departure_date->format('Ymd'), 'Reserved');
        }
        foreach ($property->availabilityBlocks()->where('source_type', 'owner')->where('blocks_booking', true)->where('block_state', 'active')->get() as $block) {
            $events[] = $this->event('block-'.$block->id, $block->starts_on->format('Ymd'), $block->ends_on->format('Ymd'), 'Unavailable');
        }
        return "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Verified Shortlet//Calendar Sync//EN\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\n".implode('', $events)."END:VCALENDAR\r\n";
    }

    private function event(string $uid, string $start, string $end, string $summary): string
    {
        return "BEGIN:VEVENT\r\nUID:{$uid}@verifiedshortlet\r\nDTSTAMP:".now()->utc()->format('Ymd\\THis\\Z')."\r\nDTSTART;VALUE=DATE:{$start}\r\nDTEND;VALUE=DATE:{$end}\r\nSUMMARY:{$summary}\r\nEND:VEVENT\r\n";
    }
}
