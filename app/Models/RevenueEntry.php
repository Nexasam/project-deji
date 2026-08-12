<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'property_id', 'booking_id', 'revenue_recognition_policy_id', 'financial_transaction_id', 'financial_document_id', 'revenue_category', 'gross_amount', 'deduction_amount', 'net_amount', 'currency', 'recognized_on', 'recognition_status', 'description', 'status', 'created_by', 'updated_by'])]
class RevenueEntry extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Revenue entries cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['gross_amount' => 'decimal:4', 'deduction_amount' => 'decimal:4', 'net_amount' => 'decimal:4', 'recognized_on' => 'date'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(RevenueRecognitionPolicy::class, 'revenue_recognition_policy_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }
}
