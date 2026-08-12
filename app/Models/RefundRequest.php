<?php

namespace App\Models;

use App\Enums\RefundRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'booking_id', 'original_payment_id', 'refund_payment_id', 'reference', 'reason_type', 'reason', 'requested_amount', 'approved_amount', 'currency', 'requested_by', 'reviewed_by', 'approved_by', 'processed_by', 'requested_at', 'reviewed_at', 'approved_at', 'processed_at', 'completed_at', 'rejection_reason', 'failure_reason', 'refund_status', 'status', 'created_by', 'updated_by'])]
class RefundRequest extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Refund requests cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['requested_amount' => 'decimal:4', 'approved_amount' => 'decimal:4', 'refund_status' => RefundRequestStatus::class, 'requested_at' => 'datetime', 'reviewed_at' => 'datetime', 'approved_at' => 'datetime', 'processed_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function originalPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'original_payment_id');
    }

    public function refundPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'refund_payment_id');
    }
}
