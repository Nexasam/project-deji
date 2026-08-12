<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'plan_key', 'provider', 'provider_subscription_id', 'subscription_status',
    'trial_ends_at', 'current_period_starts_at', 'current_period_ends_at', 'cancelled_at', 'ended_at', 'status',
])]
class BusinessSubscription extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime', 'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime', 'cancelled_at' => 'datetime', 'ended_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
