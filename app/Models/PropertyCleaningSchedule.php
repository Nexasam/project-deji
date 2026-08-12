<?php

namespace App\Models;

use App\Enums\CleaningScheduleFrequency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'property_id',
    'name',
    'frequency',
    'days_of_week',
    'preferred_start_time',
    'preferred_end_time',
    'instructions',
    'sort_order',
    'status',
])]
class PropertyCleaningSchedule extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'frequency' => CleaningScheduleFrequency::class,
            'days_of_week' => 'array',
            'sort_order' => 'integer',
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
}
