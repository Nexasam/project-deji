<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'financial_reconciliation_id', 'financial_transaction_id', 'external_reference', 'statement_date', 'statement_amount', 'transaction_amount', 'difference_amount', 'match_status', 'matched_by', 'matched_at', 'notes', 'status', 'created_by', 'updated_by'])]
class FinancialReconciliationItem extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['statement_date' => 'date', 'statement_amount' => 'decimal:4', 'transaction_amount' => 'decimal:4', 'difference_amount' => 'decimal:4', 'matched_at' => 'datetime'];
    }

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(FinancialReconciliation::class, 'financial_reconciliation_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }
}
