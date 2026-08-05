<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'actual_departure_at', 'access_return_state',
    'room_state', 'damage_state', 'deposit_state', 'released_amount', 'released_currency',
    'handover_notes', 'completed_by_employee_id', 'completed_by_user_id', 'status',
    'created_by', 'updated_by',
])]
class BookingCheckOut extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Booking check-outs are historical records and cannot be updated.');
        });

        static::deleting(function (): never {
            throw new LogicException('Booking check-outs are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'actual_departure_at' => 'datetime',
            'released_amount' => 'decimal:4',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function completedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'completed_by_employee_id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
