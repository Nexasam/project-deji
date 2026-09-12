<?php

namespace App\Services\Calendar;

use App\Models\ExternalCalendarConnection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class FetchExternalCalendar
{
    public function fetch(ExternalCalendarConnection $connection): string
    {
        $url = $connection->inboundFeedUrl();
        if (! $url) throw new RuntimeException('No import calendar URL is configured.');
        $response = Http::timeout(10)->retry(1, 200)->withOptions(['allow_redirects' => false])->get($url);
        if (! $response->successful()) throw new RuntimeException('Calendar provider returned HTTP '.$response->status().'.');
        $body = $response->body();
        if (strlen($body) > 5 * 1024 * 1024) throw new RuntimeException('Calendar feed is larger than 5 MB.');
        if (! str_contains($body, 'BEGIN:VCALENDAR')) throw new RuntimeException('Provider response is not an iCalendar feed.');
        return $body;
    }
}
