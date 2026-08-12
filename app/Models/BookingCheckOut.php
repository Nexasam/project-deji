<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'actual_departure_at', 'access_return_status', 'room_condition', 'damage_status', 'damage_notes', 'deposit_release_status', 'deposit_release_amount', 'handover_notes', 'completed_by', 'completed_at', 'status'])]
class BookingCheckOut extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['actual_departure_at' => 'datetime', 'deposit_release_amount' => 'decimal:4', 'completed_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
