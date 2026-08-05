<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'guest_id', 'operational_task_id',
    'assigned_employee_id', 'request_type', 'priority', 'description', 'resolution',
    'state', 'acknowledged_at', 'started_at', 'resolved_at', 'cancelled_at',
    'status', 'created_by', 'updated_by',
])]
class GuestServiceRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
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

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function operationalTask(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
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
