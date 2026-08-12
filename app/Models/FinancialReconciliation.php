<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'financial_account_id', 'reference', 'period_starts_on', 'period_ends_on', 'opening_balance', 'closing_balance', 'calculated_balance', 'difference_amount', 'currency', 'reconciliation_status', 'completed_by', 'completed_at', 'notes', 'status', 'created_by', 'updated_by'])]
class FinancialReconciliation extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Financial reconciliations cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['period_starts_on' => 'date', 'period_ends_on' => 'date', 'opening_balance' => 'decimal:4', 'closing_balance' => 'decimal:4', 'calculated_balance' => 'decimal:4', 'difference_amount' => 'decimal:4', 'completed_at' => 'datetime'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FinancialReconciliationItem::class);
    }
}
