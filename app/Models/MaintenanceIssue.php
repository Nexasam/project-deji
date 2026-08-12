<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'booking_id', 'asset_id', 'inspection_id', 'operational_task_id', 'reported_by', 'assigned_employee_id', 'verified_by', 'origin', 'category', 'priority', 'description', 'diagnosis', 'resolution', 'estimated_cost', 'actual_cost', 'currency', 'due_at', 'accepted_at', 'started_at', 'completed_at', 'verified_at', 'closed_at', 'status'])]
class MaintenanceIssue extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['estimated_cost' => 'decimal:4', 'actual_cost' => 'decimal:4', 'due_at' => 'datetime', 'accepted_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'verified_at' => 'datetime', 'closed_at' => 'datetime'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
