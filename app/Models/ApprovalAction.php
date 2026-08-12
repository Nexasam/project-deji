<?php

namespace App\Models;

use App\Enums\ApprovalActionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'approval_request_id', 'approval_request_step_id', 'approval_delegation_id', 'actor_user_id', 'action_type', 'comments', 'metadata', 'acted_at', 'status'])]
class ApprovalAction extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Approval actions are immutable.'));
        static::deleting(fn (): never => throw new LogicException('Approval actions cannot be deleted.'));
    }

    protected function casts(): array
    {
        return ['action_type' => ApprovalActionType::class, 'metadata' => 'array', 'acted_at' => 'datetime'];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class, 'approval_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
