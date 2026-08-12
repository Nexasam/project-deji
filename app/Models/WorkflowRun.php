<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['domain_event_id', 'business_id', 'workflow_template_id', 'property_id', 'booking_id', 'workflow_key', 'workflow_version', 'idempotency_key', 'execution_status', 'input', 'output', 'started_at', 'completed_at', 'next_retry_at', 'error_message', 'status'])]
class WorkflowRun extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['workflow_version' => 'integer', 'input' => 'array', 'output' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'next_retry_at' => 'datetime'];
    }

    public function domainEvent(): BelongsTo
    {
        return $this->belongsTo(DomainEvent::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }
}
