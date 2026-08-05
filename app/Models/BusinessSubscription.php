<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'plan_key', 'state', 'provider', 'provider_subscription_id',
    'provider_customer_id', 'trial_ends_at', 'started_at',
    'current_period_starts_at', 'current_period_ends_at', 'cancelled_at',
    'ended_at', 'metadata', 'status', 'created_by', 'updated_by',
])]
class BusinessSubscription extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'started_at' => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'ended_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
