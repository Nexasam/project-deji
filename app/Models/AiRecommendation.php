<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'booking_id', 'domain_event_id', 'workflow_run_id', 'recommendation_key', 'category', 'title', 'summary', 'rationale', 'evidence', 'proposed_action', 'priority', 'risk_level', 'recommendation_status', 'execution_status', 'confidence_score', 'model_provider', 'model_name', 'model_version', 'generated_at', 'valid_until', 'snoozed_until', 'superseded_by_id', 'accepted_by', 'accepted_at', 'dismissed_by', 'dismissed_at', 'dismissal_reason', 'executed_by', 'executed_at', 'status', 'created_by', 'updated_by'])]
class AiRecommendation extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'evidence' => 'array',
            'proposed_action' => 'array',
            'confidence_score' => 'decimal:4',
            'generated_at' => 'datetime',
            'valid_until' => 'datetime',
            'snoozed_until' => 'datetime',
            'accepted_at' => 'datetime',
            'dismissed_at' => 'datetime',
            'executed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function domainEvent(): BelongsTo
    {
        return $this->belongsTo(DomainEvent::class);
    }

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function dismissedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dismissed_by');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(AiRecommendationFeedback::class);
    }
}
