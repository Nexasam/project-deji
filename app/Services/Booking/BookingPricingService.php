<?php

namespace App\Services\Booking;

use App\Data\BookingPrice;
use App\Models\Property;
use Carbon\CarbonImmutable;

final class BookingPricingService
{
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
            ? (int) round($subtotal * ((float) $promotion->discount_value / 100)) : 0;
        $fee = (int) round(($subtotal - $discount) * .05);

        return new BookingPrice($property->pricing_currency, $nights, $subtotal, $discount, $fee, $subtotal - $discount + $fee);
    }
}
