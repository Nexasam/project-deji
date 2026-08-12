<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'cancellation_policy_id', 'requested_by', 'requester_type', 'reason', 'policy_snapshot', 'refund_amount', 'cancellation_fee', 'currency', 'refund_payment_id', 'approved_by', 'requested_at', 'approved_at', 'completed_at', 'status'])]
class BookingCancellation extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['policy_snapshot' => 'array', 'refund_amount' => 'decimal:4', 'cancellation_fee' => 'decimal:4', 'requested_at' => 'datetime', 'approved_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function refundPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'refund_payment_id');
    }
}
