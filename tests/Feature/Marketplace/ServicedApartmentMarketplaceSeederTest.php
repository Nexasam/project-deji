<?php

namespace Tests\Feature\Marketplace;

use App\Models\Business;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicedApartmentMarketplaceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_repeatably_creates_two_operators_with_five_marketplace_apartments_each(): void
    {
        $seeder = 'Database\\Seeders\\ServicedApartmentMarketplaceSeeder';
        $this->assertTrue(class_exists($seeder), 'Marketplace seeder has not been implemented.');

        $this->seed($seeder);
        $this->seed($seeder);

        $businesses = Business::query()->whereIn('email', [
            'owner@lagoonstays.test',
            'owner@coastlineresidences.test',
        ])->withCount('properties')->orderBy('email')->get();

        $this->assertCount(2, $businesses);
        $this->assertSame([5, 5], $businesses->pluck('properties_count')->all());
        $this->assertSame(10, Property::query()->whereIn('business_id', $businesses->pluck('id'))->count());

        Property::query()->whereIn('business_id', $businesses->pluck('id'))->each(function (Property $property): void {
            $this->assertSame('serviced_apartment', $property->property_type);
            $this->assertSame('entire', $property->booking_mode);
            $this->assertSame('verified', $property->verification_status->value);
            $this->assertSame('published', $property->publication_status->value);
            $this->assertGreaterThan(0, $property->beds);
            $this->assertNotNull($property->marketplaceListing);
            $this->assertTrue($property->marketplaceListing->is_publication_eligible);
            $this->assertGreaterThanOrEqual(2, $property->media()->count());
            $this->assertGreaterThanOrEqual(4, $property->amenities()->count());
        });
    }
}
