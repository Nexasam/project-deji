<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'previous_arrival_date', 'previous_departure_date', 'new_arrival_date', 'new_departure_date', 'change_type', 'availability_status', 'reason', 'approved_by', 'approved_at', 'occurred_at', 'status'])]
class BookingDateChange extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['previous_arrival_date' => 'date', 'previous_departure_date' => 'date', 'new_arrival_date' => 'date', 'new_departure_date' => 'date', 'approved_at' => 'datetime', 'occurred_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
