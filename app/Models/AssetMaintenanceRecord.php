<?php

namespace App\Models;

use App\Enums\MaintenanceRecordStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'asset_id', 'maintenance_schedule_id', 'operational_task_id',
    'supplier_id', 'status', 'scheduled_for', 'started_at', 'completed_at',
    'work_performed', 'cost_amount', 'currency', 'notes',
])]
class AssetMaintenanceRecord extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'status' => MaintenanceRecordStatus::class,
            'scheduled_for' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cost_amount' => 'decimal:4',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssetMaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    public function operationalTask(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
