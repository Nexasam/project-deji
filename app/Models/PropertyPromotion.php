<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Enums\PropertyPromotionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'name', 'public_description', 'promotion_type', 'discount_type', 'discount_value', 'coupon_code', 'booking_window_starts_on', 'booking_window_ends_on', 'stay_window_starts_on', 'stay_window_ends_on', 'applicable_weekdays', 'minimum_stay_nights', 'minimum_booking_amount', 'maximum_discount_amount', 'currency', 'total_usage_limit', 'per_guest_usage_limit', 'is_stackable', 'publication_status', 'effective_at', 'expires_at', 'status', 'created_by', 'updated_by'])]
class PropertyPromotion extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'promotion_type' => PropertyPromotionType::class,
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:4',
            'booking_window_starts_on' => 'date',
            'booking_window_ends_on' => 'date',
            'stay_window_starts_on' => 'date',
            'stay_window_ends_on' => 'date',
            'applicable_weekdays' => 'array',
            'minimum_booking_amount' => 'decimal:4',
            'maximum_discount_amount' => 'decimal:4',
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
