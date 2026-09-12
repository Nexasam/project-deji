<?php

namespace App\Services\Calendar;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final class IcalendarParser
{
    /** @return array<int, array{uid:string,starts_on:string,ends_on:string,cancelled:bool}> */
    public function parse(string $body): array
    {
        if (! str_contains($body, 'BEGIN:VCALENDAR')) throw new InvalidArgumentException('The response is not an iCalendar feed.');
        $body = preg_replace("/\r?\n[ \t]/", '', $body) ?? $body;
        preg_match_all('/BEGIN:VEVENT\R(.*?)\REND:VEVENT/s', $body, $matches);
        $events = [];
        foreach ($matches[1] as $event) {
            $values = [];
            foreach (preg_split('/\R/', $event) ?: [] as $line) {
                if (! str_contains($line, ':')) continue;
                [$key, $value] = explode(':', $line, 2);
                $values[strtoupper(explode(';', $key, 2)[0])] = trim($value);
            }
            if (! isset($values['UID'], $values['DTSTART'], $values['DTEND'])) continue;
            $start = $this->date($values['DTSTART']); $end = $this->date($values['DTEND']);
            if ($end->lessThanOrEqualTo($start)) continue;
            $events[] = ['uid' => $values['UID'], 'starts_on' => $start->toDateString(), 'ends_on' => $end->toDateString(), 'cancelled' => strtoupper($values['STATUS'] ?? '') === 'CANCELLED'];
        }
        return $events;
    }

    private function date(string $value): CarbonImmutable
    {
        if (! preg_match('/^(\d{8})/', $value, $match)) throw new InvalidArgumentException('Unsupported iCalendar event date.');
        return CarbonImmutable::createFromFormat('!Ymd', $match[1]);
    }
}
