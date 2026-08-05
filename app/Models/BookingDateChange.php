<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'booking_id', 'old_arrival_date', 'old_departure_date',
    'new_arrival_date', 'new_departure_date', 'reason', 'actor_user_id',
    'availability_state', 'metadata', 'occurred_at', 'created_by',
])]
class BookingDateChange extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Booking date changes are immutable and cannot be updated.');
        });

        static::deleting(function (): never {
            throw new LogicException('Booking date changes are immutable and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'old_arrival_date' => 'date',
            'old_departure_date' => 'date',
            'new_arrival_date' => 'date',
            'new_departure_date' => 'date',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
