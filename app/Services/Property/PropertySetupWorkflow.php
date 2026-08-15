<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\PropertySetupStep;
use App\Models\User;

final class PropertySetupWorkflow
{
    public const STEPS = [
        'basics' => true,
        'amenities' => false,
        'media' => true,
        'house-rules' => false,
        'operations' => false,
        'assets' => false,
        'documents' => false,
        'marketplace' => false,
        'review' => true,
    ];

    public function initialize(Property $property, ?User $actor = null): void
    {
        foreach (array_keys(self::STEPS) as $index => $key) {
            $step = PropertySetupStep::query()->firstOrNew(['property_id' => $property->id, 'step_key' => $key]);
            $inferredState = $step->exists ? $step->state : $this->inferState($property, $key);
            $step->fill([
                'business_id' => $property->business_id,
                'sort_order' => $index + 1,
                'is_required' => self::STEPS[$key],
                'state' => $inferredState,
                'completed_at' => $step->completed_at ?? ($inferredState === 'completed' ? now() : null),
                'status' => 'active',
                'created_by' => $step->created_by ?? $actor?->id,
                'updated_by' => $actor?->id,
            ])->save();
        }
    }

    public function complete(Property $property, string $key, User $actor): void
    {
        $this->initialize($property, $actor);
        $property->setupSteps()->where('step_key', $key)->update(['state' => 'completed', 'completed_at' => now(), 'skipped_at' => null, 'completed_by' => $actor->id, 'updated_by' => $actor->id, 'updated_at' => now()]);
    }

    public function skip(Property $property, string $key, User $actor): void
    {
        abort_if(self::STEPS[$key] ?? true, 422, 'This setup step cannot be skipped.');
        $this->initialize($property, $actor);
        $property->setupSteps()->where('step_key', $key)->update(['state' => 'skipped', 'skipped_at' => now(), 'completed_at' => null, 'completed_by' => $actor->id, 'updated_by' => $actor->id, 'updated_at' => now()]);
    }

    public function resumeKey(Property $property): string
    {
        $this->initialize($property);
        return $property->setupSteps()->whereIn('state', ['pending', 'in_progress'])->orderBy('sort_order')->value('step_key') ?? 'review';
    }

    public function nextKey(string $key): ?string
    {
        $keys = array_keys(self::STEPS);
        $position = array_search($key, $keys, true);
        return $position === false ? null : ($keys[$position + 1] ?? null);
    }

    public function routeName(string $key): string
    {
        return $key === 'basics' ? 'owner.properties.edit' : 'owner.properties.setup.'. $key;
    }

    private function inferState(Property $property, string $key): string
    {
        $complete = match ($key) {
            'basics' => $property->information_completed_at !== null,
            'amenities' => $property->amenities()->exists(),
            'media' => $property->media()->where('media_type', 'image')->exists(),
            'house-rules' => $property->houseRules()->exists(),
            'operations' => $property->cleaningSchedules()->exists(),
            'assets' => $property->assets()->exists(),
            'documents' => $property->documents()->exists(),
            'marketplace' => $property->marketplaceListing()->exists(),
            'review' => $property->publication_status->value !== 'draft',
            default => false,
        };

        return $complete ? 'completed' : 'pending';
    }
}
