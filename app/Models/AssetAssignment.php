<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'asset_id', 'property_id', 'employee_id', 'booking_id', 'operational_task_id', 'assignment_type', 'assigned_at', 'expected_return_at', 'returned_at', 'assigned_by', 'returned_by', 'notes', 'status'])]
class AssetAssignment extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::saving(function (self $assignment): void {
            if (collect(['property_id', 'employee_id', 'booking_id', 'operational_task_id'])->filter(fn ($field) => filled($assignment->{$field}))->count() !== 1) {
                throw new LogicException('An asset assignment must have exactly one target.');
            }
        });
    }

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'expected_return_at' => 'datetime', 'returned_at' => 'datetime'];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'operational_task_id');
    }
}
