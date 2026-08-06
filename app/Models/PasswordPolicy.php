<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'system_key', 'name', 'minimum_length', 'requires_uppercase',
    'requires_lowercase', 'requires_number', 'requires_symbol',
    'password_history_count', 'maximum_age_days', 'maximum_failed_attempts',
    'lockout_minutes', 'session_timeout_minutes', 'requires_mfa', 'status',
    'created_by', 'updated_by',
])]
class PasswordPolicy extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'minimum_length' => 'integer',
            'requires_uppercase' => 'boolean',
            'requires_lowercase' => 'boolean',
            'requires_number' => 'boolean',
            'requires_symbol' => 'boolean',
            'password_history_count' => 'integer',
            'maximum_age_days' => 'integer',
            'maximum_failed_attempts' => 'integer',
            'lockout_minutes' => 'integer',
            'session_timeout_minutes' => 'integer',
            'requires_mfa' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
