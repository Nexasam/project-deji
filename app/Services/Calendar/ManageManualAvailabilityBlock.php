<?php

namespace App\Services\Calendar;

use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\User;
use App\Services\Booking\PropertyAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ManageManualAvailabilityBlock
{
    public function __construct(private readonly PropertyAvailabilityService $availability) {}

    /** @param array{starts_on: string, ends_on: string, reason: string} $data */
    public function create(Property $property, User $owner, array $data): PropertyAvailabilityBlock
    {
        return DB::transaction(function () use ($property, $owner, $data): PropertyAvailabilityBlock {
            $property = Property::query()->lockForUpdate()->findOrFail($property->id);
            $start = CarbonImmutable::parse($data['starts_on']);
            $end = CarbonImmutable::parse($data['ends_on']);

            if (! $this->availability->isAvailable($property, $start, $end)) {
                throw ValidationException::withMessages([
                    'starts_on' => 'These dates conflict with an existing booking or availability block.',
                ]);
            }

            return PropertyAvailabilityBlock::query()->create([
                'business_id' => $property->business_id,
                'property_id' => $property->id,
                'source_type' => 'owner',
                'blocks_booking' => true,
                'starts_on' => $start->toDateString(),
                'ends_on' => $end->toDateString(),
                'validation_status' => 'validated',
                'validated_by' => $owner->id,
                'validated_at' => now(),
                'block_state' => 'active',
                'reason' => $data['reason'],
                'status' => 'active',
                'created_by' => $owner->id,
                'updated_by' => $owner->id,
            ]);
        });
    }

    public function release(PropertyAvailabilityBlock $block, User $owner): void
    {
        DB::transaction(function () use ($block, $owner): void {
            $block = PropertyAvailabilityBlock::query()->lockForUpdate()->findOrFail($block->id);
            if ($block->block_state !== 'active') {
                return;
            }

            $block->update([
                'block_state' => 'released',
                'released_at' => now(),
                'status' => 'released',
                'updated_by' => $owner->id,
            ]);
        });
    }
}
