<?php

namespace App\Services\Channels;

use App\Contracts\Channels\ChannelManagerProvider;
use App\Models\Business;
use RuntimeException;

final class MockChannelManagerProvider implements ChannelManagerProvider
{
    public function importListings(Business $business, string $authorisationToken): array
    {
        if (trim($authorisationToken) === '') {
            throw new RuntimeException('The host must authorise the external channel before importing listings.');
        }

        return [];
    }
}
