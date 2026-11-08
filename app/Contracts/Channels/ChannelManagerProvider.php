<?php

namespace App\Contracts\Channels;

use App\Models\Business;

interface ChannelManagerProvider
{
    /** @return array<int, array<string, mixed>> */
    public function importListings(Business $business, string $authorisationToken): array;
}
