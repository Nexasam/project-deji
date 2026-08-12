<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'property_id', 'booking_id', 'operational_task_id', 'inspector_employee_id', 'preceding_inspection_id', 'inspection_type', 'result', 'score', 'findings', 'recommendations', 'started_at', 'completed_at', 'approved_by', 'approved_at', 'status'])]
class Inspection extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'approved_at' => 'datetime'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'inspector_employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class);
    }
}
