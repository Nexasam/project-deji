<?php

namespace App\Services\Booking;

use App\Data\BookingPrice;
use App\Models\Property;
use Carbon\CarbonImmutable;
use App\Services\Platform\PlatformSettings;

final class BookingPricingService
{
    public function __construct(private PlatformSettings $settings) {}

    public function quote(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure): BookingPrice
    {
        $nights = $arrival->diffInDays($departure);
        if ($nights < 1) {
            throw new \InvalidArgumentException('A stay requires at least one night.');
        }
        $subtotal = (int) round((float) $property->default_nightly_price * 100) * $nights;
        $promotion = $property->promotions()->where('status', 'active')->where('publication_status', 'published')
            ->where(fn ($q) => $q->whereNull('effective_at')->orWhere('effective_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('minimum_stay_nights')->orWhere('minimum_stay_nights', '<=', $nights))
            ->orderByDesc('discount_value')->first();
        $discount = $promotion && $promotion->discount_type->value === 'percentage'
            ? (int) round($subtotal * ((float) $promotion->discount_value / 100))
            : 0;

        $net = $subtotal - $discount;
        $fee = 0; // Approved policy: the owner absorbs platform fees.
        $taxRate = $property->tax_enabled ? max(0, min(100, (float) $property->tax_rate)) : 0;
        $tax = (int) round($net * ($taxRate / 100));

        return new BookingPrice($property->pricing_currency, $nights, $subtotal, $discount, $fee, $tax, $net + $fee + $tax);
    }
}
