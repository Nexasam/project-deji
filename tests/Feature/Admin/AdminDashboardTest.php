<?php

namespace Tests\Feature\Admin;

use App\Models\AuditEvent;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_actionable_platform_counts(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        Business::factory()->create(['name' => 'Awaiting Operator', 'verification_status' => 'pending']);
        Business::factory()->create(['status' => 'suspended', 'verification_status' => 'verified']);
        Property::factory()->create(['name' => 'Awaiting Listing', 'publication_status' => 'pending']);
        User::factory()->create(['status' => 'suspended']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Platform overview')
            ->assertSee('Awaiting verification')
            ->assertSee('Properties to review')
            ->assertSee('Suspended businesses')
            ->assertSee('Restricted users');
    }

    public function test_property_publication_records_an_immutable_platform_audit(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        $business = Business::factory()->create();
        $property = Property::factory()->for($business)->create([
            'property_type' => 'serviced_apartment',
            'booking_mode' => 'entire',
            'capacity' => 4,
            'default_nightly_price' => 95000,
            'publication_status' => 'pending',
        ]);
        PropertyMarketplaceListing::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'slug' => 'audit-ready-home',
            'public_title' => 'Audit Ready Home',
            'publication_status' => 'draft',
            'is_publication_eligible' => false,
            'status' => 'active',
        ]);

        $this->actingAs($admin)->post(route('admin.properties.publish', $property), [
            'reason' => 'All listing information and verification documents were reviewed.',
        ])->assertRedirect(route('admin.properties.show', $property));

        $event = AuditEvent::query()->where('event_type', 'platform.property.published')->firstOrFail();
        $this->assertSame($admin->id, $event->created_by);
        $this->assertSame($property->id, $event->auditable_id);
        $this->assertSame('pending', data_get($event->before_values, 'publication_status'));
        $this->assertSame('published', data_get($event->after_values, 'publication_status'));
        $this->assertSame('All listing information and verification documents were reviewed.', data_get($event->metadata, 'reason'));

        $this->expectException(\LogicException::class);
        $event->update(['description' => 'Changed']);
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
