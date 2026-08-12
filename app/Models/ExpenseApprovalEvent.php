<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'expense_id', 'action', 'previous_status', 'new_status', 'reason', 'occurred_at', 'status', 'created_by', 'updated_by'])]
class ExpenseApprovalEvent extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Expense approval history cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
