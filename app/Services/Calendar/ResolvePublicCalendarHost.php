<?php

namespace App\Services\Calendar;

use RuntimeException;

class ResolvePublicCalendarHost
{
    /** @return list<string> */
    public function resolve(string $host): array
    {
        $records = dns_get_record($host, DNS_A | DNS_AAAA);
        $addresses = [];

        foreach ($records ?: [] as $record) {
            $address = $record['ip'] ?? $record['ipv6'] ?? null;
            if ($address !== null) {
                $addresses[] = $address;
            }
        }

        if ($addresses === []) {
            throw new RuntimeException('The calendar provider host could not be resolved.');
        }

        foreach (array_unique($addresses) as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new RuntimeException('The calendar provider resolved to a non-public address.');
            }
        }

        return array_values(array_unique($addresses));
    }
}
