<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\BusinessRolePermissionSetting;
use App\Models\Permission;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_configure_role_module_access_for_their_business(): void
    {
        [$owner] = $this->ownerWithBusiness('Nexa Stays');
        $role = Role::query()->where('system_key', 'accountant')->firstOrFail();

        $this->actingAs($owner)->patch(route('owner.team.roles.permissions.update', $role), [
            'modules' => $this->modules([
                'dashboard' => 'hidden',
                'finance' => 'read',
            ]),
        ])->assertRedirect(route('owner.team.index'));

        $this->assertDatabaseHas('business_role_permission_settings', [
            'role_id' => $role->id,
            'permission_id' => Permission::query()->where('key', 'finance.view_transactions')->value('id'),
            'effect' => 'allow',
        ]);
        $this->assertDatabaseHas('business_role_permission_settings', [
            'role_id' => $role->id,
            'permission_id' => Permission::query()->where('key', 'expense.create')->value('id'),
            'effect' => 'deny',
        ]);
        $this->assertDatabaseHas('business_role_permission_settings', [
            'role_id' => $role->id,
            'permission_id' => Permission::query()->where('key', 'business.view')->value('id'),
            'effect' => 'deny',
        ]);
    }

    public function test_role_read_only_access_allows_page_view_but_blocks_write_actions(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create();
        $role = Role::query()->where('system_key', 'accountant')->firstOrFail();
        $staff = $this->staffWithRole($business, $role);

        $this->actingAs($owner)->patch(route('owner.team.roles.permissions.update', $role), [
            'modules' => $this->modules([
                'dashboard' => 'hidden',
                'finance' => 'read',
                'properties' => 'read',
            ]),
        ])->assertRedirect();

        $this->actingAs($staff)->get(route('owner.dashboard'))->assertForbidden();
        $this->get(route('owner.entry'))->assertRedirect(route('owner.properties.index'));
        $this->get(route('owner.finance'))->assertOk();
        $this->post(route('owner.finance.expenses.store'), [
            'property_id' => $property->id,
            'category' => 'cleaning',
            'amount' => '25000',
            'incurred_on' => '2026-09-22',
            'description' => 'Read-only accountant should not create this',
        ])->assertForbidden();
    }

    public function test_non_owner_cannot_change_role_capability_matrix(): void
    {
        [, $business] = $this->ownerWithBusiness('Nexa Stays');
        $role = Role::query()->where('system_key', 'accountant')->firstOrFail();
        $staff = $this->staffWithRole($business, $role);

        $this->actingAs($staff)->patch(route('owner.team.roles.permissions.update', $role), [
            'modules' => $this->modules(['finance' => 'write']),
        ])->assertForbidden();

        $this->assertSame(0, BusinessRolePermissionSetting::query()->count());
    }

    public function test_team_page_explains_role_capabilities_and_property_scope(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $membership = $business->memberships()->where('user_id', $owner->id)->firstOrFail();

        $this->actingAs($owner)->get(route('owner.team.index'))
            ->assertOk()
            ->assertSee('Role capability matrix')
            ->assertSee('<details class="mt-5 overflow-hidden', false)
            ->assertDontSee('<details open', false)
            ->assertSee(route('owner.team.show', $membership), false)
            ->assertSee('View access')
            ->assertSee('Dashboard analytics')
            ->assertSee('Read &amp; write', false)
            ->assertSee('Property scope');
    }

    public function test_owner_can_create_custom_business_role_and_configure_it(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');

        $this->actingAs($owner)->post(route('owner.team.roles.store'), [
            'name' => 'Night Supervisor',
            'description' => 'Overnight operations and arrival support.',
        ])->assertRedirect(route('owner.team.index'));

        $role = Role::query()
            ->where('business_id', $business->id)
            ->where('slug', 'night-supervisor')
            ->firstOrFail();

        $this->assertFalse($role->is_template);
        $this->assertNull($role->system_key);

        $this->actingAs($owner)->get(route('owner.team.index'))
            ->assertOk()
            ->assertSee('Night Supervisor')
            ->assertSee('Custom role');

        $this->actingAs($owner)->patch(route('owner.team.roles.permissions.update', $role), [
            'modules' => $this->modules([
                'bookings' => 'read',
                'operations' => 'write',
            ]),
        ])->assertRedirect(route('owner.team.index'));

        $this->assertDatabaseHas('business_role_permission_settings', [
            'business_id' => $business->id,
            'role_id' => $role->id,
            'permission_id' => Permission::query()->where('key', 'task.assign')->value('id'),
            'effect' => 'allow',
        ]);
        $this->assertDatabaseHas('business_role_permission_settings', [
            'business_id' => $business->id,
            'role_id' => $role->id,
            'permission_id' => Permission::query()->where('key', 'booking.create')->value('id'),
            'effect' => 'deny',
        ]);
    }

    public function test_team_member_has_a_dedicated_access_page(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $role = Role::query()->where('system_key', 'cleaner')->firstOrFail();
        $staff = $this->staffWithRole($business, $role);
        $membership = $business->memberships()->where('user_id', $staff->id)->firstOrFail();

        $this->actingAs($owner)->get(route('owner.team.show', $membership))
            ->assertOk()
            ->assertSee('Dedicated team member access')
            ->assertSee($staff->name)
            ->assertSee('Role and property access')
            ->assertSee('Functional access from role')
            ->assertSee('Cleaner')
            ->assertSee('Property scope');
    }

    public function test_owner_can_reactivate_deactivated_team_member_with_new_scope(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Ikoyi Test Flat']);
        $cleanerRole = Role::query()->where('system_key', 'cleaner')->firstOrFail();
        $staff = $this->staffWithRole($business, $cleanerRole);
        $membership = $business->memberships()->where('user_id', $staff->id)->firstOrFail();

        $this->actingAs($owner)->delete(route('owner.team.deactivate', $membership))
            ->assertRedirect(route('owner.team.index'));

        $membership->refresh();
        $this->assertSame('inactive', $membership->status->value);
        $this->assertSame('inactive', UserBusinessContext::query()->where('business_membership_id', $membership->id)->firstOrFail()->status);

        $this->actingAs($owner)->get(route('owner.team.index'))
            ->assertOk()
            ->assertSee('Reactivate', false);

        $this->actingAs($owner)->patch(route('owner.team.reactivate', $membership), [
            'job_title' => 'Night operations lead',
            'role_id' => $cleanerRole->id,
            'property_ids' => [$property->id],
        ])->assertRedirect(route('owner.team.index'));

        $membership->refresh();
        $this->assertSame('active', $membership->status->value);
        $this->assertSame('Night operations lead', $membership->job_title);
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $staff->id,
            'role_id' => $cleanerRole->id,
            'business_membership_id' => $membership->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('user_business_contexts', [
            'user_id' => $staff->id,
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('property_staff_assignments', [
            'business_id' => $business->id,
            'property_id' => $property->id,
            'assignment_status' => 'active',
            'status' => 'active',
        ]);
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }

    private function staffWithRole(Business $business, Role $role): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $membership = BusinessMembership::query()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'job_title' => $role->name,
            'status' => 'active',
            'joined_at' => now(),
        ]);
        $assignment = UserRole::query()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'business_membership_id' => $membership->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);
        UserBusinessContext::query()->create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'active_user_role_id' => $assignment->id,
            'switched_at' => now(),
            'status' => 'active',
        ]);

        return $user;
    }

    /** @param array<string, string> $overrides */
    private function modules(array $overrides = []): array
    {
        return array_merge([
            'dashboard' => 'hidden',
            'properties' => 'hidden',
            'bookings' => 'hidden',
            'calendar' => 'hidden',
            'finance' => 'hidden',
            'operations' => 'hidden',
            'team' => 'hidden',
        ], $overrides);
    }
}
