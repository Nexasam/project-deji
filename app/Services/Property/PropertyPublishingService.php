<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class PropertyPublishingService
{
    public function publish(Property $property, User $admin): Property
    {
        return DB::transaction(function () use ($property, $admin): Property {
            $property = Property::query()->with('marketplaceListing')->lockForUpdate()->findOrFail($property->id);
            $listing = $property->marketplaceListing;

            if ($listing === null || blank($listing->slug) || blank($listing->public_title)) {
                throw ValidationException::withMessages([
                    'property' => 'Complete the marketplace title and listing before publishing this property.',
                ]);
            }

            if ($property->property_type !== 'serviced_apartment' || $property->booking_mode !== 'entire') {
                throw ValidationException::withMessages([
                    'property' => 'This MVP can only publish entire serviced apartments.',
                ]);
            }

            if ((float) $property->default_nightly_price <= 0 || $property->capacity < 1) {
                throw ValidationException::withMessages([
                    'property' => 'Add a valid nightly price and guest capacity before publishing.',
                ]);
            }

            $now = now();
            $property->update([
                'verification_status' => 'verified',
                'verified_at' => $now,
                'verified_by' => $admin->id,
                'publication_status' => 'published',
                'published_at' => $now,
                'published_by' => $admin->id,
                'readiness_status' => 'ready',
                'operational_status' => 'available',
                'operational_status_updated_at' => $now,
                'updated_by' => $admin->id,
            ]);
            $listing->update([
                'publication_status' => 'published',
                'is_publication_eligible' => true,
                'publication_eligibility_details' => ['approved_by_platform_admin' => true],
                'eligibility_checked_at' => $now,
                'published_by' => $admin->id,
                'published_at' => $now,
                'unpublished_by' => null,
                'unpublished_at' => null,
                'status' => 'active',
                'updated_by' => $admin->id,
            ]);

            return $property->fresh(['business', 'marketplaceListing']);
        });
    }

    public function unpublish(Property $property, User $admin): Property
    {
        return DB::transaction(function () use ($property, $admin): Property {
            $property = Property::query()->with('marketplaceListing')->lockForUpdate()->findOrFail($property->id);
            $now = now();

            $property->update([
                'publication_status' => 'unpublished',
                'updated_by' => $admin->id,
            ]);
            $property->marketplaceListing?->update([
                'publication_status' => 'unpublished',
                'is_publication_eligible' => false,
                'unpublished_by' => $admin->id,
                'unpublished_at' => $now,
                'updated_by' => $admin->id,
            ]);

            return $property->fresh(['business', 'marketplaceListing']);
        });
    }
}
