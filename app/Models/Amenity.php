<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['code', 'name', 'category', 'status', 'created_by', 'updated_by'])]
class Amenity extends Model
{
    use HasFactory, HasUuids;

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class)
            ->withPivot(['business_id', 'details'])
            ->withTimestamps();
    }
}
