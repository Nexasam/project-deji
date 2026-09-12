<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyPromotion;
use App\Models\Asset;
use App\Models\PropertyChannelConnection;
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
        $this->post($url(9), ['channels' => ['airbnb']])->assertRedirect($url(10));
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

    public function test_basics_only_accept_flat_duplex_or_apartment(): void
    {
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);
        $url = "/owner/properties/{$property->id}/create/step3";

        $this->actingAs($user)->from($url)->post($url, [
            'property_type' => 'villa', 'booking_mode' => 'entire',
            'capacity' => 4, 'bedrooms' => 2, 'bathrooms' => 2,
        ])->assertRedirect($url)->assertSessionHasErrors('property_type');

        $this->post($url, [
            'property_type' => 'flat', 'booking_mode' => 'entire',
            'capacity' => 4, 'bedrooms' => 2, 'bathrooms' => 2,
        ])->assertRedirect("/owner/properties/{$property->id}/create/step4");
        $this->assertSame('flat', $property->fresh()->property_type);
    }

    public function test_uploaded_photo_is_stored_and_rendered_when_media_step_is_reopened(): void
    {
        Storage::fake('public');
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);

        $this->actingAs($user)->post("/owner/properties/{$property->id}/create/step5", [
            'media' => [UploadedFile::fake()->image('client-lounge.jpg')],
        ])->assertRedirect("/owner/properties/{$property->id}/create/step6");

        $media = $property->media()->sole();
        Storage::disk('public')->assertExists($media->storage_path);
        $this->get("/owner/properties/{$property->id}/create/step5")
            ->assertOk()->assertSee($media->storage_path, false)->assertSee('client-lounge');
    }

    public function test_custom_assets_are_persisted_and_removed_selections_become_inactive(): void
    {
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);
        $url = "/owner/properties/{$property->id}/create/step6";

        $this->actingAs($user)->post($url, ['assets' => '["smart-tv","Standing fan"]']);
        $this->assertDatabaseHas('assets', ['property_id' => $property->id, 'name' => 'Standing fan', 'status' => 'active']);

        $this->post($url, ['assets' => '["Standing fan","Blender"]'])->assertRedirect("/owner/properties/{$property->id}/create/step7");
        $this->assertDatabaseHas('assets', ['property_id' => $property->id, 'name' => 'smart-tv', 'status' => 'inactive']);
        $this->assertDatabaseHas('assets', ['property_id' => $property->id, 'name' => 'Blender', 'status' => 'active']);
        $this->assertSame(2, Asset::query()->where('property_id', $property->id)->where('status', 'active')->count());
    }

    public function test_owner_can_preview_their_uploaded_document_but_other_business_cannot(): void
    {
        Storage::fake('public');
        [$user, $business] = $this->owner();
        [$otherOwner] = $this->owner('Other Stays');
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);
        $this->actingAs($user)->post("/owner/properties/{$property->id}/create/step7", [
            'documents' => [UploadedFile::fake()->createWithContent('inspection.pdf', '%PDF-1.4 preview')],
        ]);
        $document = $property->documents()->sole();

        $this->get(route('owner.properties.wizard.documents.preview', [$property, $document]))
            ->assertOk()->assertHeader('content-disposition');
        $this->actingAs($otherOwner)->get(route('owner.properties.wizard.documents.preview', [$property, $document]))
            ->assertNotFound();
    }

    public function test_selected_channels_are_saved_as_pending_database_connections(): void
    {
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft']);
        $url = "/owner/properties/{$property->id}/create/step9";

        $this->actingAs($user)->post($url, ['channels' => ['airbnb', 'bookingcom']]);
        $this->assertDatabaseHas('property_channel_connections', ['property_id' => $property->id, 'provider' => 'airbnb', 'connection_status' => 'pending', 'status' => 'active']);
        $this->assertDatabaseHas('property_channel_connections', ['property_id' => $property->id, 'provider' => 'bookingcom', 'connection_status' => 'pending', 'status' => 'active']);

        $this->post($url, ['channels' => ['whatsapp']]);
        $this->assertDatabaseHas('property_channel_connections', ['property_id' => $property->id, 'provider' => 'airbnb', 'status' => 'inactive']);
        $this->assertDatabaseHas('property_channel_connections', ['property_id' => $property->id, 'provider' => 'whatsapp', 'status' => 'active']);
        $this->assertSame(1, PropertyChannelConnection::query()->where('property_id', $property->id)->where('status', 'active')->count());
    }

    public function test_review_page_shows_saved_amenities_assets_documents_images_and_channels(): void
    {
        Storage::fake('public');
        [$user, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['publication_status' => 'draft', 'name' => 'Complete Review Flat']);
        $this->actingAs($user)->post("/owner/properties/{$property->id}/create/step5", ['media' => [UploadedFile::fake()->image('review-room.jpg')]]);
        $this->post("/owner/properties/{$property->id}/create/step6", ['assets' => '["Standing fan"]']);
        $this->post("/owner/properties/{$property->id}/create/step7", ['documents' => [UploadedFile::fake()->createWithContent('lease.pdf', '%PDF-1.4 lease')]]);
        $this->post("/owner/properties/{$property->id}/create/step9", ['channels' => ['airbnb']]);

        $this->get("/owner/properties/{$property->id}/create/step10")
            ->assertOk()->assertSee('Complete Review Flat')->assertSee('Standing fan')
            ->assertSee('lease')->assertSee('review-room')->assertSee('Airbnb');
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
