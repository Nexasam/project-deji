<?php

namespace App\Models;

use App\Enums\PricingAdjustmentType;
use App\Enums\PricingRuleType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'name', 'rule_type', 'adjustment_type', 'adjustment_value', 'currency', 'starts_on', 'ends_on', 'applicable_weekdays', 'minimum_stay_nights', 'maximum_stay_nights', 'minimum_advance_booking_days', 'maximum_advance_booking_days', 'priority', 'is_stackable', 'effective_at', 'expires_at', 'status', 'created_by', 'updated_by'])]
class PropertyPricingRule extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'rule_type' => PricingRuleType::class,
            'adjustment_type' => PricingAdjustmentType::class,
            'adjustment_value' => 'decimal:4',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'applicable_weekdays' => 'array',
            'is_stackable' => 'boolean',
            'effective_at' => 'datetime',
            'expires_at' => 'datetime',
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
