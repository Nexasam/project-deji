<?php

namespace App\Models;

use App\Enums\ApprovalRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['business_id', 'approval_workflow_id', 'reference', 'subject_type', 'subject_id', 'requested_by', 'request_status', 'current_step_order', 'subject_snapshot', 'request_reason', 'submitted_at', 'due_at', 'completed_at', 'cancelled_at', 'status'])]
class ApprovalRequest extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['request_status' => ApprovalRequestStatus::class, 'subject_snapshot' => 'array', 'submitted_at' => 'datetime', 'due_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalRequestStep::class)->orderBy('step_order');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class);
    }
}
