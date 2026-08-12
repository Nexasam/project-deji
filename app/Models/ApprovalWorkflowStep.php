<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['approval_workflow_id', 'step_order', 'name', 'role_id', 'permission_id', 'required_approvals', 'minimum_amount', 'maximum_amount', 'currency', 'escalate_after_minutes', 'conditions', 'status'])]
class ApprovalWorkflowStep extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['minimum_amount' => 'decimal:4', 'maximum_amount' => 'decimal:4', 'conditions' => 'array'];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
