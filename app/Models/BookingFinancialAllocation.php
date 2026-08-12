<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'payment_id', 'expense_id', 'allocation_type', 'direction', 'amount', 'currency', 'recognized_on', 'description', 'status'])]
class BookingFinancialAllocation extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'recognized_on' => 'date'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
