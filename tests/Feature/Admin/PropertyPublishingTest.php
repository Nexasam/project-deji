<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_uses_dedicated_admin_endpoint(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Platform administration')
            ->assertSee(route('admin.login.store'), false);
    }

    public function test_platform_admin_login_lands_on_property_publishing(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->platformAdmin();
        $admin->update(['password' => 'AdminPassword123!']);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'AdminPassword123!',
        ])->assertRedirect(route('admin.properties.index', absolute: false));
    }

    public function test_non_admin_cannot_sign_in_through_admin_login(): void
    {
        $owner = User::factory()->create(['password' => 'Password123!']);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $owner->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_only_platform_admin_can_open_property_review_workspace(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->platformAdmin();
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $property = $this->pendingProperty();

        $this->get(route('admin.properties.index'))->assertRedirect(route('login'));
        $this->actingAs($owner)->get(route('admin.properties.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.properties.index'))
            ->assertOk()
            ->assertSee('Property publishing')
            ->assertSee($property->name)
            ->assertSee($property->business->name);
    }

    public function test_platform_admin_publishes_property_and_listing_atomically(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->platformAdmin();
        $property = $this->pendingProperty();

        $this->actingAs($admin)->post(route('admin.properties.publish', $property))
            ->assertRedirect(route('admin.properties.show', $property))
            ->assertSessionHas('status', 'Property published to the marketplace.');

        $property->refresh();
        $listing = $property->marketplaceListing()->firstOrFail();

        $this->assertSame('published', $property->publication_status->value);
        $this->assertSame('verified', $property->verification_status->value);
        $this->assertSame('ready', $property->readiness_status->value);
        $this->assertSame('available', $property->operational_status->value);
        $this->assertSame($admin->id, $property->published_by);
        $this->assertSame('published', $listing->publication_status);
        $this->assertTrue($listing->is_publication_eligible);
        $this->assertSame($admin->id, $listing->published_by);

        $this->get(route('home'))->assertSee($listing->public_title);
    }

    public function test_platform_admin_can_unpublish_a_property(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->platformAdmin();
        $property = $this->pendingProperty();
        $this->actingAs($admin)->post(route('admin.properties.publish', $property));

        $this->actingAs($admin)->post(route('admin.properties.unpublish', $property))
            ->assertRedirect(route('admin.properties.show', $property));

        $property->refresh();
        $listing = $property->marketplaceListing()->firstOrFail();
        $this->assertSame('unpublished', $property->publication_status->value);
        $this->assertSame('unpublished', $listing->publication_status);
        $this->assertFalse($listing->is_publication_eligible);
        $this->assertSame($admin->id, $listing->unpublished_by);
        $this->get(route('home'))->assertDontSee($listing->public_title);
    }

    public function test_property_without_marketplace_listing_cannot_be_published(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->platformAdmin();
        $business = Business::factory()->create();
        $property = Property::factory()->for($business)->create(['publication_status' => 'pending']);

        $this->actingAs($admin)->from(route('admin.properties.show', $property))
            ->post(route('admin.properties.publish', $property))
            ->assertRedirect(route('admin.properties.show', $property))
            ->assertSessionHasErrors('property');

        $this->assertSame('pending', $property->fresh()->publication_status->value);
    }

    private function platformAdmin(): User
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $role = Role::query()->where('system_key', 'platform_super_admin')->firstOrFail();
        UserRole::query()->create([
            'user_id' => $admin->id,
            'role_id' => $role->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        return $admin;
    }

    private function pendingProperty(): Property
    {
        $business = Business::factory()->create(['name' => 'Client Demo Stays']);
        $property = Property::factory()->for($business)->create([
            'name' => 'Client Review Apartment',
            'property_type' => 'serviced_apartment',
            'booking_mode' => 'entire',
            'publication_status' => 'pending',
            'verification_status' => 'pending',
            'readiness_status' => 'ready',
            'operational_status' => 'available',
            'status' => 'active',
        ]);
        PropertyMarketplaceListing::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'slug' => 'client-review-apartment',
            'public_title' => 'Client Review Apartment',
            'short_summary' => 'A client review serviced apartment.',
            'publication_status' => 'draft',
            'is_publication_eligible' => false,
            'status' => 'active',
        ]);

        return $property->load('business', 'marketplaceListing');
    }
}
