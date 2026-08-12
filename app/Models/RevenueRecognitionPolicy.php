<?php

namespace App\Models;

use App\Enums\RevenueRecognitionBasis;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'name', 'recognition_basis', 'rules', 'is_default', 'effective_from', 'effective_until', 'status', 'created_by', 'updated_by'])]
class RevenueRecognitionPolicy extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['recognition_basis' => RevenueRecognitionBasis::class, 'rules' => 'array', 'is_default' => 'boolean', 'effective_from' => 'date', 'effective_until' => 'date'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RevenueEntry::class);
    }
}
