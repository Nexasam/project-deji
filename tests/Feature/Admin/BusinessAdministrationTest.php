<?php

namespace Tests\Feature\Admin;

use App\Models\AuditEvent;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Booking\CreateMarketplaceBooking;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BusinessAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_admin_can_search_and_verify_a_business_with_an_audit(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        $business = Business::factory()->create([
            'name' => 'Oceanview Hospitality',
            'verification_status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('admin.businesses.index', ['q' => 'Oceanview']))
            ->assertOk()
            ->assertSee('Business directory')
            ->assertSee('Oceanview Hospitality');

        $this->actingAs($admin)->post(route('admin.businesses.verify', $business), [
            'reason' => 'Registration and operator information have been reviewed.',
        ])->assertRedirect(route('admin.businesses.show', $business));

        $this->assertSame('verified', $business->fresh()->verification_status->value);
        $event = AuditEvent::query()->where('event_type', 'platform.business.verified')->firstOrFail();
        $this->assertSame('pending', data_get($event->before_values, 'verification_status'));
        $this->assertSame('verified', data_get($event->after_values, 'verification_status'));
    }

    public function test_suspension_removes_inventory_and_blocks_direct_booking_then_reactivation_restores_it(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        $business = Business::factory()->verified()->create(['name' => 'Safety Test Stays']);
        $property = Property::factory()->for($business)->create([
            'name' => 'Safety Test Apartment',
            'property_type' => 'serviced_apartment',
            'booking_mode' => 'entire',
            'verification_status' => 'verified',
            'publication_status' => 'published',
            'readiness_status' => 'ready',
            'operational_status' => 'available',
            'status' => 'active',
        ]);
        PropertyMarketplaceListing::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'slug' => 'safety-test-apartment',
            'public_title' => 'Safety Test Apartment',
            'publication_status' => 'published',
            'is_publication_eligible' => true,
            'status' => 'active',
        ]);

        $this->get(route('home'))->assertSee('Safety Test Apartment');

        $this->actingAs($admin)->post(route('admin.businesses.suspend', $business), [
            'reason' => 'Urgent safety review is required before accepting new guests.',
        ])->assertRedirect(route('admin.businesses.show', $business));

        $this->get(route('home'))->assertDontSee('Safety Test Apartment');
        $this->assertSame('suspended', $business->fresh()->status->value);

        try {
            app(CreateMarketplaceBooking::class)->handle(User::factory()->create(), $property, []);
            $this->fail('A suspended business accepted a direct booking attempt.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('property', $exception->errors());
        }

        $this->actingAs($admin)->post(route('admin.businesses.reactivate', $business), [
            'reason' => 'Safety review completed and operating access is restored.',
        ])->assertRedirect(route('admin.businesses.show', $business));

        $this->get(route('home'))->assertSee('Safety Test Apartment');
        $this->assertSame('active', $business->fresh()->status->value);
    }

    public function test_support_admin_has_read_only_business_access(): void
    {
        $this->seed(AccessControlSeeder::class);
        $support = $this->administrator('platform_support_admin');
        $business = Business::factory()->create(['verification_status' => 'pending']);

        $this->actingAs($support)->get(route('admin.businesses.show', $business))->assertOk();
        $response = $this->actingAs($support)->post(route('admin.businesses.verify', $business), [
            'reason' => 'This action should not be available to support staff.',
        ]);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('pending', $business->fresh()->verification_status->value);
    }

    public function test_business_state_changes_require_a_useful_reason(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        $business = Business::factory()->create(['verification_status' => 'pending']);

        $this->actingAs($admin)->from(route('admin.businesses.show', $business))
            ->post(route('admin.businesses.reject', $business), ['reason' => 'No'])
            ->assertSessionHasErrors('reason');

        $this->assertSame('pending', $business->fresh()->verification_status->value);
        $this->assertDatabaseCount('audit_events', 0);
    }

    private function administrator(string $roleKey): User
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        UserRole::query()->create([
            'user_id' => $admin->id,
            'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        return $admin;
    }
}
