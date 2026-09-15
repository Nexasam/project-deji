<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\User;
use App\Services\Platform\PlatformAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PropertyPublishingService
{
    public function __construct(private readonly PlatformAudit $audit) {}

    public function publish(Property $property, User $admin, string $reason): Property
    {
        return DB::transaction(function () use ($property, $admin, $reason): Property {
            $property = Property::query()->with('marketplaceListing')->lockForUpdate()->findOrFail($property->id);
            $listing = $property->marketplaceListing;
            $before = $this->snapshot($property);

            if ($listing === null) {
                $listing = $property->marketplaceListing()->create([
                    'business_id' => $property->business_id,
                    'slug' => Str::slug($property->name).'-'.Str::lower(Str::random(6)),
                    'public_title' => $property->name,
                    'short_summary' => Str::limit((string) $property->description, 500),
                    'public_description' => $property->description,
                    'publication_status' => 'draft', 'is_publication_eligible' => false, 'status' => 'active',
                    'created_by' => $admin->id, 'updated_by' => $admin->id,
                ]);
            }

            if ($listing === null || blank($listing->slug) || blank($listing->public_title)) {
                throw ValidationException::withMessages([
                    'property' => 'Complete the marketplace title and listing before publishing this property.',
                ]);
            }

            if (! in_array($property->property_type, ['serviced_apartment', 'flat', 'duplex', 'apartment'], true) || $property->booking_mode !== 'entire') {
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

            $property->refresh();
            $this->audit->record(
                $admin,
                'platform.property.published',
                $property,
                "Published {$property->name} to the marketplace.",
                $before,
                $this->snapshot($property),
                ['reason' => $reason],
            );

            return $property->fresh(['business', 'marketplaceListing']);
        });
    }

    public function unpublish(Property $property, User $admin, string $reason): Property
    {
        return DB::transaction(function () use ($property, $admin, $reason): Property {
            $property = Property::query()->with('marketplaceListing')->lockForUpdate()->findOrFail($property->id);
            $now = now();
            $before = $this->snapshot($property);

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

            $property->refresh();
            $this->audit->record($admin, 'platform.property.unpublished', $property, "Removed {$property->name} from the marketplace.", $before, $this->snapshot($property), ['reason' => $reason]);

            return $property->fresh(['business', 'marketplaceListing']);
        });
    }

    public function reject(Property $property, User $admin, string $reason): Property
    {
        return DB::transaction(function () use ($property, $admin, $reason): Property {
            $property = Property::query()->with('marketplaceListing')->lockForUpdate()->findOrFail($property->id);
            $now = now();
            $before = $this->snapshot($property);

            $property->update([
                'verification_status' => 'rejected',
                'verified_at' => null,
                'verified_by' => null,
                'publication_status' => 'unpublished',
                'published_at' => null,
                'published_by' => null,
                'operational_status' => 'unavailable',
                'operational_status_updated_at' => $now,
                'updated_by' => $admin->id,
            ]);
            $property->marketplaceListing?->update([
                'publication_status' => 'unpublished',
                'is_publication_eligible' => false,
                'publication_eligibility_details' => [
                    'approved_by_platform_admin' => false,
                    'rejection_reason' => $reason,
                    'rejected_by' => $admin->id,
                    'rejected_at' => $now->toIso8601String(),
                ],
                'eligibility_checked_at' => $now,
                'unpublished_by' => $admin->id,
                'unpublished_at' => $now,
                'updated_by' => $admin->id,
            ]);

            $property->refresh();
            $this->audit->record($admin, 'platform.property.rejected', $property, "Rejected {$property->name} and returned it for correction.", $before, $this->snapshot($property), ['reason' => $reason]);

            return $property->fresh(['business', 'marketplaceListing']);
        });
    }

    /** @return array<string, mixed> */
    private function snapshot(Property $property): array
    {
        return [
            'publication_status' => $property->publication_status->value,
            'verification_status' => $property->verification_status->value,
            'readiness_status' => $property->readiness_status->value,
            'operational_status' => $property->operational_status?->value,
        ];
    }
}
