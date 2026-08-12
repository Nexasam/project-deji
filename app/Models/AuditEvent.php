<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

#[Fillable([
    'business_id', 'auditable_type', 'auditable_id', 'event_type', 'description',
    'before_values', 'after_values', 'metadata', 'source_channel', 'actor_type',
    'correlation_id', 'request_id', 'device_identifier', 'ip_address', 'user_agent',
    'occurred_at', 'status', 'created_by',
])]
class AuditEvent extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Audit events are immutable.');
        });
        static::deleting(function (): never {
            throw new LogicException('Audit events cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'before_values' => 'array',
            'after_values' => 'array',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
