<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'operational_task_id', 'employee_id', 'assignment_role', 'assignment_status', 'assigned_by', 'assigned_at', 'accepted_at', 'rejected_at', 'rejection_reason', 'released_at', 'release_reason', 'status'])]
class OperationalTaskAssignment extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'accepted_at' => 'datetime', 'rejected_at' => 'datetime', 'released_at' => 'datetime'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'operational_task_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
