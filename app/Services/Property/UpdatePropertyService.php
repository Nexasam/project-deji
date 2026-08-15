<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdatePropertyService
{
    public function update(Property $property, User $actor, array $attributes): Property
    {
        return DB::transaction(function () use ($property, $actor, $attributes): Property {
            $property->update(array_merge($attributes, [
                'information_completed_at' => $property->information_completed_at ?? now(),
                'updated_by' => $actor->id,
            ]));

            return $property->refresh();
        });
    }
}
