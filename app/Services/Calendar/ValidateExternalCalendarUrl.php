<?php

namespace App\Services\Calendar;

use Illuminate\Validation\ValidationException;

final class ValidateExternalCalendarUrl
{
    public function validate(string $provider, string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);
        $allowed = ['airbnb' => 'airbnb.com', 'bookingcom' => 'booking.com'];
        $host = strtolower($parts['host'] ?? '');
        $base = $allowed[$provider] ?? null;
        $validHost = $base && ($host === $base || str_ends_with($host, '.'.$base));

        if (($parts['scheme'] ?? '') !== 'https' || ! $validHost || filter_var($host, FILTER_VALIDATE_IP)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])) {
            throw ValidationException::withMessages(['feed_url' => 'Enter a valid HTTPS calendar export URL from the selected provider.']);
        }

        return $url;
    }
}
