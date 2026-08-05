<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'identity_state', 'balance_state',
    'deposit_state', 'access_state', 'access_reference', 'checklist', 'blocking_issues',
    'completed_at', 'cancelled_at', 'completed_by_employee_id', 'completed_by_user_id',
    'status', 'created_by', 'updated_by',
])]
class BookingCheckIn extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Booking check-ins are historical records and cannot be updated.');
        });

        static::deleting(function (): never {
            throw new LogicException('Booking check-ins are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'blocking_issues' => 'array',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
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
