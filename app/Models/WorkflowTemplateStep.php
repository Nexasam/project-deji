<?php

namespace App\Models;

use App\Enums\OperationalTaskType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['workflow_template_id', 'step_key', 'name', 'description', 'step_order', 'step_type', 'task_type', 'responsible_role_id', 'responsible_department_id', 'estimated_duration_minutes', 'sla_minutes', 'requires_verification', 'requires_approval', 'checklist_template', 'failure_configuration', 'escalation_configuration', 'configuration', 'status'])]
class WorkflowTemplateStep extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['task_type' => OperationalTaskType::class, 'requires_verification' => 'boolean', 'requires_approval' => 'boolean', 'checklist_template' => 'array', 'failure_configuration' => 'array', 'escalation_configuration' => 'array', 'configuration' => 'array'];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function responsibleRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'responsible_role_id');
    }

    public function responsibleDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'responsible_department_id');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowStep::class);
    }
}
