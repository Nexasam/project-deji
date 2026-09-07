<?php

namespace Tests\Feature\Marketplace;

use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MarketplaceSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_serviced_apartment_inventory_fields_are_available_and_cast(): void
    {
        $this->assertTrue(Schema::hasColumn('properties', 'beds'));
        $this->assertTrue(Schema::hasColumn('property_marketplace_listings', 'stay_categories'));

        $property = new Property(['beds' => '3']);
        $listing = new PropertyMarketplaceListing(['stay_categories' => ['family', 'business']]);

        $this->assertSame(3, $property->beds);
        $this->assertSame(['family', 'business'], $listing->stay_categories);
    }
}
