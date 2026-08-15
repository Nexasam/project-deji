<?php

namespace App\Services\Property;

use App\Models\Property;

final class PropertyPublicationReadinessService
{
    /** @return list<string> */
    public function setupBlockers(Property $property): array
    {
        $blockers = [];
        $address = $property->address ?? [];

        if (! in_array($property->property_type, ['apartment', 'short_let'], true)) $blockers[] = 'Choose Serviced apartment or Short-let as the property type.';
        if (blank($property->name) || blank(data_get($address, 'line_1')) || blank(data_get($address, 'city')) || blank(data_get($address, 'state'))) $blockers[] = 'Complete the property name and address.';
        if ($property->capacity < 1) $blockers[] = 'Add a valid guest capacity.';
        if ((float) $property->default_nightly_price <= 0 || blank($property->pricing_currency)) $blockers[] = 'Add a nightly price and currency.';
        return $blockers;
    }

    /** @return list<string> */
    public function marketplaceBlockers(Property $property): array
    {
        $blockers = $this->setupBlockers($property);
        if (! $property->media()->where('media_type', 'image')->where('status', 'active')->exists()) $blockers[] = 'Upload at least one property image.';
        $listing = $property->marketplaceListing;
        if ($listing === null || blank($listing->public_title) || blank($listing->public_description) || blank($listing->check_in_time) || blank($listing->check_out_time)) $blockers[] = 'Complete the marketplace title, description, check-in and check-out times.';
        return $blockers;
    }

    /** @return list<string> */
    public function warnings(Property $property): array
    {
        return array_values(array_filter([
            $property->amenities()->exists() ? null : 'No amenities selected.',
            $property->houseRules()->exists() ? null : 'No house rules added.',
            $property->cleaningSchedules()->exists() ? null : 'No cleaning schedule added.',
            $property->assets()->exists() ? null : 'No assets recorded.',
            $property->documents()->exists() ? null : 'No property documents uploaded.',
            $property->marketplaceListing()->exists() ? null : 'Marketplace listing details have not been added.',
        ]));
    }
}
