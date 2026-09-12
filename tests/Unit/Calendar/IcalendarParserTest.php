<?php

namespace Tests\Unit\Calendar;

use App\Services\Calendar\IcalendarParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IcalendarParserTest extends TestCase
{
    public function test_it_parses_all_day_events_folded_uids_and_cancellations(): void
    {
        $calendar = "BEGIN:VCALENDAR\r\nBEGIN:VEVENT\r\nUID:long-event-\r\n 123\r\nDTSTART;VALUE=DATE:20261003\r\nDTEND;VALUE=DATE:20261006\r\nEND:VEVENT\r\nBEGIN:VEVENT\r\nUID:cancelled-1\r\nDTSTART:20261101T120000Z\r\nDTEND:20261103T120000Z\r\nSTATUS:CANCELLED\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";

        $events = (new IcalendarParser)->parse($calendar);

        $this->assertSame('long-event-123', $events[0]['uid']);
        $this->assertSame('2026-10-03', $events[0]['starts_on']);
        $this->assertSame('2026-10-06', $events[0]['ends_on']);
        $this->assertFalse($events[0]['cancelled']);
        $this->assertTrue($events[1]['cancelled']);
    }

    public function test_it_rejects_malformed_or_non_calendar_content(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new IcalendarParser)->parse('<html>not a calendar</html>');
    }
}
