<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'name', 'policy_type', 'description', 'rules', 'is_default', 'effective_from', 'effective_until', 'status'])]
class CancellationPolicy extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['rules' => 'array', 'is_default' => 'boolean', 'effective_from' => 'date', 'effective_until' => 'date'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
