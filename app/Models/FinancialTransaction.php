<?php

namespace App\Models;

use App\Enums\FinancialTransactionDirection;
use App\Enums\FinancialTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'financial_account_id', 'property_id', 'booking_id', 'payment_id', 'expense_id', 'tax_category_id', 'exchange_rate_id', 'reversed_transaction_id', 'transaction_group_id', 'reference', 'transaction_type', 'direction', 'economic_category', 'source_type', 'source_id', 'amount', 'currency', 'base_amount', 'base_currency', 'occurred_at', 'effective_on', 'due_at', 'settled_at', 'description', 'metadata', 'transaction_status', 'status', 'created_by', 'updated_by'])]
class FinancialTransaction extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Financial transactions cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['transaction_type' => FinancialTransactionType::class, 'direction' => FinancialTransactionDirection::class, 'amount' => 'decimal:4', 'base_amount' => 'decimal:4', 'occurred_at' => 'datetime', 'effective_on' => 'date', 'due_at' => 'datetime', 'settled_at' => 'datetime', 'metadata' => 'array'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversed_transaction_id');
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(self::class, 'reversed_transaction_id');
    }
}
