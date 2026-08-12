<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'platform_user_id', 'authorized_by',
    'reason', 'ip_address', 'user_agent', 'started_at', 'authorized_at',
    'expires_at', 'ended_at',
    'termination_reason', 'status', 'created_by', 'updated_by',
])]
class ImpersonationSession extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'authorized_at' => 'datetime',
            'expires_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function platformUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'platform_user_id');
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
