<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'financial_document_id', 'sequence', 'purpose', 'amount', 'paid_amount', 'currency', 'due_at', 'paid_at', 'payment_status', 'notes', 'status', 'created_by', 'updated_by'])]
class BookingPaymentInstallment extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['sequence' => 'integer', 'amount' => 'decimal:4', 'paid_amount' => 'decimal:4', 'due_at' => 'datetime', 'paid_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function financialDocument(): BelongsTo
    {
        return $this->belongsTo(BookingFinancialDocument::class, 'financial_document_id');
    }
}
