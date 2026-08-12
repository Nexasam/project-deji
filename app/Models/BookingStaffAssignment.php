<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'booking_id', 'employee_id', 'assignment_role', 'responsibilities', 'is_primary', 'assigned_at', 'ended_at', 'assignment_status', 'status', 'created_by', 'updated_by'])]
class BookingStaffAssignment extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'assigned_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
