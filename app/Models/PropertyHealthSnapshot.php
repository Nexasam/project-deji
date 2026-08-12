<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'property_id', 'snapshot_date', 'period_type', 'score', 'occupancy_score', 'guest_rating_score', 'maintenance_score', 'cleaning_score', 'revenue_growth_score', 'asset_condition_score', 'inspection_score', 'booking_conversion_score', 'component_details', 'supporting_metrics', 'ai_explanation', 'recommended_improvements', 'calculation_version', 'calculated_at', 'status', 'created_by', 'updated_by'])]
class PropertyHealthSnapshot extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Property health history cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'score' => 'integer',
            'component_details' => 'array',
            'supporting_metrics' => 'array',
            'recommended_improvements' => 'array',
            'calculated_at' => 'datetime',
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
