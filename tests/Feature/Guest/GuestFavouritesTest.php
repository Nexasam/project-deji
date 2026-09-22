<?php

namespace Tests\Feature\Guest;

use App\Models\GuestFavourite;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestFavouritesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ServicedApartmentMarketplaceSeeder::class);
    }

    public function test_guest_can_save_and_view_a_favourite_property(): void
    {
        $guest = User::factory()->create();
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();

        $this->actingAs($guest)
            ->postJson(route('guest.favourites.store', $property))
            ->assertOk()
            ->assertJson([
                'favourited' => true,
                'message' => 'Saved to favourites.',
            ]);

        $this->assertDatabaseHas('guest_favourites', [
            'user_id' => $guest->id,
            'property_id' => $property->id,
        ]);

        $this->get(route('guest.favourites.index'))
            ->assertOk()
            ->assertSee('Favourites')
            ->assertSee('Admiralty Waterfront Residence')
            ->assertSee(route('marketplace.show', $property->marketplaceListing->slug), false);
    }

    public function test_guest_can_remove_a_favourite_property(): void
    {
        $guest = User::factory()->create();
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();

        GuestFavourite::query()->create([
            'user_id' => $guest->id,
            'property_id' => $property->id,
            'favourited_at' => now(),
        ]);

        $this->actingAs($guest)
            ->deleteJson(route('guest.favourites.destroy', $property))
            ->assertOk()
            ->assertJson([
                'favourited' => false,
                'message' => 'Removed from favourites.',
            ]);

        $this->assertDatabaseMissing('guest_favourites', [
            'user_id' => $guest->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_guest_cannot_favourite_an_unpublished_property(): void
    {
        $guest = User::factory()->create();
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();
        $property->update(['publication_status' => 'draft']);

        $this->actingAs($guest)
            ->postJson(route('guest.favourites.store', $property))
            ->assertNotFound();

        $this->assertDatabaseMissing('guest_favourites', [
            'user_id' => $guest->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_guest_navigation_exposes_favourites(): void
    {
        $guest = User::factory()->create();

        $this->actingAs($guest)
            ->get(route('guest.dashboard'))
            ->assertOk()
            ->assertSee('Favourites')
            ->assertSee(route('guest.favourites.index'), false);
    }

    public function test_guest_must_login_before_saving_a_favourite(): void
    {
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();

        $this->post(route('guest.favourites.store', $property))
            ->assertRedirect(route('login'));
    }
}
