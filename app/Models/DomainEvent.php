<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'property_id', 'booking_id', 'event_name', 'aggregate_type', 'aggregate_id', 'correlation_id', 'causation_id', 'idempotency_key', 'payload', 'metadata', 'occurred_at', 'publication_status', 'attempt_count', 'published_at', 'next_attempt_at', 'failure_reason', 'status'])]
class DomainEvent extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (DomainEvent $event): void {
            if ($event->isDirty(['event_name', 'aggregate_type', 'aggregate_id', 'payload', 'occurred_at'])) {
                throw new LogicException('Published business facts cannot be rewritten.');
            }
        });
        static::deleting(function (): never {
            throw new LogicException('Domain events cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['payload' => 'array', 'metadata' => 'array', 'occurred_at' => 'datetime', 'attempt_count' => 'integer', 'published_at' => 'datetime', 'next_attempt_at' => 'datetime'];
    }

    public function workflowRuns(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }

    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }
}
