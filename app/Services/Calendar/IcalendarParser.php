<?php

namespace App\Services\Calendar;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final class IcalendarParser
{
    /** @return array<int, array{uid:?string,starts_on:?string,ends_on:?string,cancelled:bool,valid:bool,errors:list<string>}> */
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
            $errors = [];
            foreach (['UID', 'DTSTART', 'DTEND'] as $required) {
                if (! isset($values[$required]) || $values[$required] === '') $errors[] = "Missing {$required}.";
            }
            $start = $end = null;
            if ($errors === []) {
                try {
                    $start = $this->date($values['DTSTART']);
                    $end = $this->date($values['DTEND']);
                    if ($end->lessThanOrEqualTo($start)) $errors[] = 'DTEND must be after DTSTART.';
                } catch (InvalidArgumentException $error) {
                    $errors[] = $error->getMessage();
                }
            }
            $events[] = [
                'uid' => $values['UID'] ?? null,
                'starts_on' => $start?->toDateString(),
                'ends_on' => $end?->toDateString(),
                'cancelled' => strtoupper($values['STATUS'] ?? '') === 'CANCELLED',
                'valid' => $errors === [],
                'errors' => $errors,
            ];
        }
        return $events;
    }

    private function date(string $value): CarbonImmutable
    {
        if (! preg_match('/^(\d{8})/', $value, $match)) throw new InvalidArgumentException('Unsupported iCalendar event date.');
        return CarbonImmutable::createFromFormat('!Ymd', $match[1]);
    }
}
