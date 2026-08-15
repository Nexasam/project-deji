<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SyncPropertyAmenitiesService
{
    /** @param list<string> $amenityIds */
    public function sync(Property $property, User $actor, array $amenityIds): void
    {
        DB::transaction(function () use ($property, $actor, $amenityIds): void {
            PropertyAmenity::query()
                ->where('property_id', $property->id)
                ->whereNotIn('amenity_id', $amenityIds)
                ->delete();

            foreach ($amenityIds as $sortOrder => $amenityId) {
                $assignment = PropertyAmenity::withTrashed()->firstOrNew([
                    'property_id' => $property->id,
                    'amenity_id' => $amenityId,
                ]);

                $assignment->fill([
                    'business_id' => $property->business_id,
                    'sort_order' => $sortOrder,
                    'status' => 'active',
                ]);
                $assignment->deleted_at = null;
                $assignment->save();
            }

            $property->forceFill(['updated_by' => $actor->id])->save();
        });
    }
}
