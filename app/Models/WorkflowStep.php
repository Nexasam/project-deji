<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['workflow_run_id', 'workflow_template_step_id', 'step_order', 'step_key', 'handler_key', 'execution_status', 'input', 'output', 'attempt_count', 'started_at', 'completed_at', 'next_retry_at', 'error_message', 'status'])]
class WorkflowStep extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['step_order' => 'integer', 'input' => 'array', 'output' => 'array', 'attempt_count' => 'integer', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'next_retry_at' => 'datetime'];
    }

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }

    public function templateStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplateStep::class, 'workflow_template_step_id');
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }
}
