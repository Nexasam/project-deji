<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\User;
use App\Support\ActiveBusinessContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreatePropertyService
{
    public function create(ActiveBusinessContext $context, User $actor, array $attributes): Property
    {
        return DB::transaction(function () use ($context, $actor, $attributes): Property {
            return Property::query()->create(array_merge($attributes, [
                'business_id' => $context->business->id,
                'code' => $this->uniqueCode($context, $attributes['name']),
                'pricing_currency' => $context->business->currency,
                'verification_status' => 'unverified',
                'maintenance_status' => 'not_required',
                'publication_status' => 'draft',
                'readiness_status' => 'not_ready',
                'operational_status' => 'unavailable',
                'operational_status_updated_at' => now(),
                'information_completed_at' => now(),
                'status' => 'active',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]));
        });
    }

    private function uniqueCode(ActiveBusinessContext $context, string $name): string
    {
        $prefix = Str::upper(Str::substr(Str::slug($name, ''), 0, 12)) ?: 'PROPERTY';

        do {
            $code = $prefix.'-'.Str::upper(Str::random(6));
        } while (Property::query()->where('business_id', $context->business->id)->where('code', $code)->exists());

        return $code;
    }
}
