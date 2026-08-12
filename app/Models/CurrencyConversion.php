<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'exchange_rate_id', 'financial_transaction_id', 'source_type', 'source_id', 'source_amount', 'source_currency', 'rate', 'converted_amount', 'target_currency', 'converted_at', 'status', 'created_by', 'updated_by'])]
class CurrencyConversion extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Currency conversion evidence cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['source_amount' => 'decimal:4', 'rate' => 'decimal:10', 'converted_amount' => 'decimal:4', 'converted_at' => 'datetime'];
    }

    public function exchangeRate(): BelongsTo
    {
        return $this->belongsTo(CurrencyExchangeRate::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }
}
