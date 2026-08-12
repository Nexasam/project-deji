<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'booking_id', 'guest_user_id', 'operational_task_id', 'assigned_employee_id', 'request_type', 'priority', 'description', 'resolution', 'requested_at', 'resolved_at', 'status'])]
class GuestServiceRequest extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['requested_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'operational_task_id');
    }
}
