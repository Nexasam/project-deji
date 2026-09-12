<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\PropertyPromotion;
use App\Services\Booking\BookingPricingService;
use App\Services\Booking\PropertyAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmed_booking_blocks_overlapping_but_not_adjacent_dates(): void
    {
        $property = Property::factory()->create();
        Booking::factory()->create(['business_id' => $property->business_id, 'property_id' => $property->id,
            'arrival_date' => '2026-10-10', 'departure_date' => '2026-10-13', 'status' => 'confirmed']);
        $service = app(PropertyAvailabilityService::class);

        $this->assertFalse($service->isAvailable($property, CarbonImmutable::parse('2026-10-12'), CarbonImmutable::parse('2026-10-14')));
        $this->assertTrue($service->isAvailable($property, CarbonImmutable::parse('2026-10-13'), CarbonImmutable::parse('2026-10-15')));
    }

    public function test_quote_applies_weekly_discount_then_five_percent_service_fee(): void
    {
        $property = Property::factory()->create(['default_nightly_price' => 100000, 'pricing_currency' => 'NGN']);
        PropertyPromotion::create(['business_id' => $property->business_id, 'property_id' => $property->id,
            'name' => 'Weekly', 'promotion_type' => 'length_of_stay', 'discount_type' => 'percentage',
            'discount_value' => 10, 'minimum_stay_nights' => 7, 'publication_status' => 'published',
            'effective_at' => now()->subDay(), 'expires_at' => now()->addMonth(), 'status' => 'active']);

        $quote = app(BookingPricingService::class)->quote($property, CarbonImmutable::parse('2026-10-01'), CarbonImmutable::parse('2026-10-08'));
        $this->assertSame(70000000, $quote->subtotalMinor);
        $this->assertSame(7000000, $quote->discountMinor);
        $this->assertSame(3150000, $quote->serviceFeeMinor);
        $this->assertSame(66150000, $quote->totalMinor);
    }

    public function test_active_manual_block_prevents_booking_without_duplicating_availability_days(): void
    {
        $property = Property::factory()->create();
        PropertyAvailabilityBlock::query()->create([
            'business_id' => $property->business_id, 'property_id' => $property->id,
            'source_type' => 'owner', 'blocks_booking' => true,
            'starts_on' => '2026-10-10', 'ends_on' => '2026-10-13',
            'validation_status' => 'valid', 'block_state' => 'active',
            'reason' => 'Maintenance', 'status' => 'active',
        ]);

        $service = app(PropertyAvailabilityService::class);

        $this->assertFalse($service->isAvailable($property, CarbonImmutable::parse('2026-10-12'), CarbonImmutable::parse('2026-10-14')));
        $this->assertTrue($service->isAvailable($property, CarbonImmutable::parse('2026-10-13'), CarbonImmutable::parse('2026-10-15')));
    }
}
