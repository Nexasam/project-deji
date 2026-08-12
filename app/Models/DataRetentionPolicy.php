<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'policy_key', 'name', 'data_category', 'retention_days', 'retention_trigger', 'terminal_action', 'legal_basis', 'allow_legal_hold', 'is_active', 'configuration', 'status'])]
class DataRetentionPolicy extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['allow_legal_hold' => 'boolean', 'is_active' => 'boolean', 'configuration' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(DataRetentionExecution::class);
    }
}
