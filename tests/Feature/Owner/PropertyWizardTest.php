<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyPromotion;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_property_wizard(): void
    {
        $this->get('/owner/properties/create/step1')->assertRedirect('/login');
    }

    public function test_legacy_property_workflow_entries_resolve_to_the_full_page_wizard(): void
    {
        [$user] = $this->owner();

        $this->actingAs($user)->get('/owner/properties/create/wizard')
            ->assertRedirect('/owner/properties/create/step1');
        $this->get('/owner/preview/property-amenities')
            ->assertRedirect('/owner/properties/create/step1');
        $this->assertFalse(Route::has('owner.properties.store'));
    }

    public function test_owner_starts_a_scoped_draft_and_saves_location(): void
    {
        [$user, $business] = $this->owner();

        $response = $this->actingAs($user)->post('/owner/properties/create/step1', [
            'property_kind' => 'brand_new',
        ]);

        $property = Property::query()->sole();
        $this->assertSame($business->id, $property->business_id);
        $response->assertRedirect("/owner/properties/{$property->id}/create/step2");

        $this->actingAs($user)->post("/owner/properties/{$property->id}/create/step2", [
            'state' => 'Lagos', 'city' => 'Lekki', 'address_line' => '12 Admiralty Way',
        ])->assertRedirect("/owner/properties/{$property->id}/create/step3");

        $this->assertSame('Lekki', $property->fresh()->address['city']);
    }

    public function test_other_business_cannot_open_a_draft(): void
    {
        [$owner] = $this->owner();
        [$otherOwner] = $this->owner('Other Stays');
        $property = Property::factory()->for(Business::query()->where('name', 'Nexa Stays')->firstOrFail())->create();

        $this->actingAs($otherOwner)->get("/owner/properties/{$property->id}/create/step2")->assertNotFound();
    }

    public function test_every_scoped_wizard_screen_renders_for_its_owner(): void
    {
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);

        foreach (range(2, 10) as $step) {
            $this->actingAs($user)->get("/owner/properties/{$property->id}/create/step{$step}")->assertOk();
        }
    }

    public function test_owner_can_complete_the_full_wizard_and_submit_once(): void
    {
        Storage::fake('public');
        [$user] = $this->owner();
        $this->actingAs($user)->post('/owner/properties/create/step1', ['property_kind' => 'brand_new']);
        $property = Property::query()->sole();
        $url = fn (int $step) => "/owner/properties/{$property->id}/create/step{$step}";

        $this->post($url(2), ['state' => 'Lagos', 'city' => 'Lekki', 'address_line' => '12 Admiralty Way'])->assertRedirect($url(3));
        $this->post($url(3), ['property_type' => 'apartment', 'booking_mode' => 'entire', 'capacity' => 4, 'bedrooms' => 2, 'bathrooms' => 2])->assertRedirect($url(4));
        $this->post($url(4), ['amenity_codes' => '[]'])->assertRedirect($url(5));
        $this->post($url(5), ['media' => [UploadedFile::fake()->image('living-room.jpg')]])->assertRedirect($url(6));
        $this->post($url(6), ['assets' => '["smart-tv"]'])->assertRedirect($url(7));
        $this->post($url(7).'/skip')->assertRedirect($url(8));
        $this->post($url(8), ['name' => 'Harbour View', 'description' => 'A verified stay.', 'default_nightly_price' => 120000, 'discount_percentage' => 10, 'minimum_stay_nights' => 7])->assertRedirect($url(9));
        $this->post($url(9), ['channels' => ['airbnb' => 'https://airbnb.example/listing/1']])->assertRedirect($url(10));
        $this->get($url(10))->assertOk()->assertSee('Harbour View')->assertDontSee('SomeMovels Property');
        $this->post("/owner/properties/{$property->id}/create/submit")->assertRedirect("/owner/properties/{$property->id}/create/success");
        $this->post("/owner/properties/{$property->id}/create/submit")->assertRedirect("/owner/properties/{$property->id}/create/success");

        $property->refresh();
        $this->assertSame('pending', $property->publication_status->value);
        $this->assertSame(1, $property->lifecycleEvents()->where('event_type', 'marketplace_verification_submitted')->count());
        $this->assertSame(1, $property->channelConnections()->count());
        $this->assertSame('10.0000', PropertyPromotion::query()->where('property_id', $property->id)->value('discount_value'));
    }

    public function test_owner_can_remove_wizard_media(): void
    {
        Storage::fake('public');
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);
        $this->actingAs($user)->post("/owner/properties/{$property->id}/create/step5", ['media' => [UploadedFile::fake()->image('room.jpg')]]);
        $media = $property->media()->firstOrFail();

        $this->delete("/owner/properties/{$property->id}/create/media/{$media->id}")
            ->assertRedirect("/owner/properties/{$property->id}/create/step5");

        $this->assertSoftDeleted('property_media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->storage_path);
    }

    private function owner(string $name = 'Nexa Stays'): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create();
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }
}
