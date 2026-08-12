<?php

namespace App\Models;

use App\Enums\OperationalTaskStatus;
use App\Enums\OperationalTaskType;
use App\Enums\TaskGenerationSource;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'workflow_run_id', 'workflow_step_id', 'assigned_employee_id', 'parent_task_id',
    'reference', 'title', 'task_type', 'priority', 'status', 'due_at', 'started_at',
    'completed_at', 'estimated_duration_minutes', 'sla_due_at', 'requires_verification',
    'verified_by', 'verified_at', 'verification_notes', 'notes', 'manual_creation_reason',
    'generation_source', 'is_recurring', 'recurrence_rule', 'next_recurrence_at',
    'escalated_at', 'generation_metadata',
])]
class OperationalTask extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'task_type' => OperationalTaskType::class,
            'priority' => TaskPriority::class,
            'status' => OperationalTaskStatus::class,
            'generation_source' => TaskGenerationSource::class,
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'array',
            'generation_metadata' => 'array',
            'due_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'sla_due_at' => 'datetime',
            'requires_verification' => 'boolean',
            'verified_at' => 'datetime',
            'next_recurrence_at' => 'datetime',
            'escalated_at' => 'datetime',
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

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_task_id');
    }

    public function childTasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_task_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(OperationalTaskChecklistItem::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(OperationalTaskDependency::class);
    }

    public function dependants(): HasMany
    {
        return $this->hasMany(OperationalTaskDependency::class, 'depends_on_task_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OperationalTaskAttachment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OperationalTaskAssignment::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
