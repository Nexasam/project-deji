<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'code', 'name', 'tax_type', 'rate', 'is_inclusive', 'effective_from', 'effective_until', 'status', 'created_by', 'updated_by'])]
class TaxCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['rate' => 'decimal:4', 'is_inclusive' => 'boolean', 'effective_from' => 'date', 'effective_until' => 'date'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
