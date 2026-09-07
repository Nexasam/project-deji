<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyMarketplaceListing;
use App\Models\PropertyMedia;
use App\Models\PropertyPromotion;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicedApartmentMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([AccessControlSeeder::class, AmenitySeeder::class]);

        foreach ($this->operators() as $operator) {
            DB::transaction(function () use ($operator): void {
                $owner = User::query()->updateOrCreate(['email' => $operator['email']], [
                    'name' => $operator['owner'], 'password' => 'Password123!',
                    'email_verified_at' => now(), 'timezone' => 'Africa/Lagos', 'status' => 'active',
                ]);
                $business = Business::query()->where('email', $operator['email'])->first()
                    ?? app(BusinessOnboardingService::class)->onboard($owner, [
                        'name' => $operator['business'], 'email' => $operator['email'],
                        'country_code' => 'NG', 'business_type' => 'serviced_apartments',
                        'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
                    ]);

                foreach ($operator['apartments'] as $index => $data) {
                    $this->seedApartment($business, $owner, $data, $index);
                }
            });
        }
    }

    private function seedApartment(Business $business, User $owner, array $data, int $index): void
    {
        $property = Property::query()->updateOrCreate(['code' => $data['code']], [
            'business_id' => $business->id, 'name' => $data['name'],
            'address' => ['line_1' => $data['address'], 'city' => $data['area'], 'state' => 'Lagos', 'country_code' => 'NG'],
            'property_type' => 'serviced_apartment', 'booking_mode' => 'entire',
            'capacity' => $data['capacity'], 'bedrooms' => $data['bedrooms'], 'beds' => $data['beds'],
            'bathrooms' => $data['bathrooms'], 'description' => $data['description'],
            'default_nightly_price' => $data['price'], 'pricing_currency' => 'NGN',
            'verification_status' => 'verified', 'publication_status' => 'published',
            'readiness_status' => 'ready', 'operational_status' => 'available',
            'maintenance_status' => 'not_required', 'status' => 'active',
            'verified_at' => now(), 'published_at' => now(), 'created_by' => $owner->id, 'updated_by' => $owner->id,
        ]);

        PropertyMarketplaceListing::withTrashed()->updateOrCreate(['property_id' => $property->id], [
            'business_id' => $business->id, 'slug' => $data['slug'], 'public_title' => $data['name'],
            'short_summary' => $data['description'], 'public_description' => $data['description'],
            'stay_categories' => $data['categories'], 'check_in_time' => '14:00', 'check_out_time' => '11:00',
            'instant_booking_enabled' => true, 'publication_status' => 'published',
            'is_publication_eligible' => true, 'published_by' => $owner->id, 'published_at' => now(),
            'status' => 'active', 'deleted_at' => null, 'created_by' => $owner->id, 'updated_by' => $owner->id,
        ]);

        foreach ([1, 2] as $position) {
            PropertyMedia::withTrashed()->updateOrCreate([
                'property_id' => $property->id, 'sort_order' => $position,
            ], [
                'business_id' => $business->id, 'media_type' => 'image',
                'external_url' => '/apt'.((($index + $position - 1) % 3) + 1).'.jpg',
                'title' => $data['name'], 'alt_text' => $data['name'].' serviced apartment',
                'is_primary' => $position === 1, 'status' => 'active', 'deleted_at' => null,
            ]);
        }

        $amenities = Amenity::query()->whereIn('code', ['wifi', 'air-conditioning', 'kitchen', 'power-backup', 'security', 'free-parking'])->get();
        foreach ($amenities as $sort => $amenity) {
            PropertyAmenity::withTrashed()->updateOrCreate([
                'property_id' => $property->id, 'amenity_id' => $amenity->id,
            ], ['business_id' => $business->id, 'sort_order' => $sort, 'status' => 'active', 'deleted_at' => null]);
        }

        if ($index === 0) {
            PropertyPromotion::withTrashed()->updateOrCreate([
                'property_id' => $property->id, 'name' => 'Weekly stay saving',
            ], [
                'business_id' => $business->id, 'public_description' => 'Save 10% on stays of seven nights or more.',
                'promotion_type' => 'length_of_stay', 'discount_type' => 'percentage', 'discount_value' => 10,
                'minimum_stay_nights' => 7, 'currency' => 'NGN', 'publication_status' => 'published',
                'effective_at' => now()->startOfDay(), 'expires_at' => now()->addYear(),
                'status' => 'active', 'deleted_at' => null, 'created_by' => $owner->id, 'updated_by' => $owner->id,
            ]);
        }
    }

    private function operators(): array
    {
        $apartments = [
            ['LAG','Admiralty Waterfront Residence','lekki-admiralty-waterfront','Lekki','12 Admiralty Way',95000,2,3,4,2,['lekki','beachfront','family'],'A bright waterfront apartment with reliable power and workspace.','1522708323590'],
            ['LAG','Ikoyi Executive Maisonette','ikoyi-executive-maisonette','Ikoyi','8 Bourdillon Road',180000,3,4,6,3,['ikoyi','business','family'],'A spacious premium stay for families and executive trips.','1600607687939'],
            ['LAG','Victoria Island City Suite','victoria-island-city-suite','Victoria Island','21 Akin Adesola Street',125000,2,2,4,2,['victoria-island','business'],'A calm city base near restaurants and commercial districts.','1560448204'],
            ['LAG','Lekki Family Garden Apartment','lekki-family-garden','Lekki','4 Freedom Way',110000,3,4,6,3,['lekki','family'],'A secure family apartment with generous living areas.','1600566753086'],
            ['LAG','Oniru Beachside Studio','oniru-beachside-studio','Victoria Island','6 Ligali Ayorinde Street',75000,1,1,2,1,['victoria-island','beachfront','business'],'A polished studio minutes from Oniru Beach.','1505693416388'],
            ['CSR','Banana Island Harbour Flat','banana-island-harbour-flat','Ikoyi','3 Banana Island Road',220000,3,4,6,3.5,['ikoyi','beachfront','family'],'A refined harbour-facing apartment with premium facilities.','1600210492486'],
            ['CSR','Eko Atlantic Business Stay','eko-atlantic-business-stay','Victoria Island','10 Eko Boulevard',160000,2,2,4,2,['victoria-island','beachfront','business'],'Modern serviced living for business travellers.','1600607688969'],
            ['CSR','Chevron Family Residence','chevron-family-residence','Lekki','18 Chevron Drive',105000,3,4,6,3,['lekki','family'],'A comfortable family residence in a guarded estate.','1600566753190'],
            ['CSR','Old Ikoyi Quiet Retreat','old-ikoyi-quiet-retreat','Ikoyi','15 Glover Road',145000,2,3,4,2,['ikoyi','business','family'],'Quiet serviced accommodation surrounded by greenery.','1600585154340'],
            ['CSR','Elegushi Coastal Apartment','elegushi-coastal-apartment','Lekki','9 Oba Elegushi Road',90000,2,2,4,2,['lekki','beachfront'],'An easy coastal stay close to the beach and nightlife.','1493809842364'],
        ];
        $make = fn (array $row, int $i): array => array_combine(['prefix','name','slug','area','address','price','bedrooms','beds','capacity','bathrooms','categories','description','photo'], $row) + ['code' => $row[0].'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)];

        return [
            ['owner' => 'Amara Okafor', 'email' => 'owner@lagoonstays.test', 'business' => 'Lagoon Stays', 'apartments' => array_map($make, array_slice($apartments, 0, 5), array_keys(array_slice($apartments, 0, 5)))],
            ['owner' => 'Tunde Balogun', 'email' => 'owner@coastlineresidences.test', 'business' => 'Coastline Residences', 'apartments' => array_map($make, array_slice($apartments, 5), array_keys(array_slice($apartments, 5)))],
        ];
    }
}
