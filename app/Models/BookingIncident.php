<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'reporter_user_id', 'reporter_employee_id',
    'operational_task_id', 'document_id', 'incident_type', 'severity', 'description',
    'financial_impact', 'financial_currency', 'resolution', 'reported_at', 'resolved_at',
    'state', 'status', 'created_by', 'updated_by',
])]
class BookingIncident extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Booking incidents are immutable and cannot be updated.');
        });

        static::deleting(function (): never {
            throw new LogicException('Booking incidents are immutable and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'financial_impact' => 'decimal:4',
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
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

    public function reporterUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reporterEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporter_employee_id');
    }

    public function operationalTask(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
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
