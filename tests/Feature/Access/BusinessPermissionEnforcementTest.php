<?php

namespace Tests\Feature\Access;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\MembershipPermissionOverride;
use App\Models\OperationalTask;
use App\Models\Permission;
use App\Models\Property;
use App\Models\PropertyStaffAssignment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class BusinessPermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AccessControlSeeder::class);
    }

    public function test_business_roles_receive_only_their_permitted_workspaces(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Role Test Stays');
        Property::factory()->for($business)->create();

        [$manager] = $this->staffWithRole($business, 'property_manager');
        $this->actingAs($manager)->get(route('owner.properties.index'))->assertOk();
        $this->get(route('owner.bookings'))->assertOk();
        $this->get(route('owner.finance'))->assertForbidden();

        [$reception] = $this->staffWithRole($business, 'reception_officer');
        $this->actingAs($reception)->get(route('owner.bookings'))->assertOk();
        $this->get(route('owner.operations'))->assertForbidden();

        [$accountant] = $this->staffWithRole($business, 'accountant');
        $this->actingAs($accountant)->get(route('owner.finance'))->assertOk();
        $this->get(route('owner.calendar'))->assertForbidden();

        $this->actingAs($owner)->get(route('owner.team.index'))->assertOk();
    }

    public function test_property_assignment_scopes_lists_direct_records_and_mutations(): void
    {
        [, $business] = $this->ownerWithBusiness('Scoped Stays');
        $visible = Property::factory()->for($business)->create(['name' => 'Assigned Residence']);
        $hidden = Property::factory()->for($business)->create(['name' => 'Hidden Residence']);
        [$manager] = $this->staffWithRole($business, 'property_manager', [$visible]);

        $this->actingAs($manager)->get(route('owner.properties.index'))
            ->assertOk()
            ->assertSee('Assigned Residence')
            ->assertDontSee('Hidden Residence');
        $this->get(route('owner.properties.show', $visible))->assertOk();
        $this->get(route('owner.properties.show', $hidden))->assertForbidden();

        $this->post(route('owner.operations.tasks.store'), [
            'property_id' => $hidden->id,
            'title' => 'Must not be created',
            'task_type' => 'cleaning',
            'priority' => 'normal',
            'due_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ])->assertForbidden();
        $this->assertDatabaseMissing('operational_tasks', ['title' => 'Must not be created']);
    }

    public function test_membership_deny_override_wins_and_allow_override_can_extend_a_role(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Override Stays');
        $property = Property::factory()->for($business)->create();
        [$reception, $membership] = $this->staffWithRole($business, 'reception_officer');

        $this->override($membership, 'property.view', 'deny', $owner);
        $this->actingAs($reception)->get(route('owner.properties.show', $property))->assertForbidden();

        $this->override($membership, 'finance.view_transactions', 'allow', $owner);
        $this->get(route('owner.finance'))->assertOk();
    }

    public function test_owner_can_invite_assign_scope_and_deactivate_a_team_member(): void
    {
        Notification::fake();
        [$owner, $business] = $this->ownerWithBusiness('Team Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Team Property']);
        $role = Role::query()->where('system_key', 'operations_manager')->firstOrFail();

        $this->actingAs($owner)->post(route('owner.team.store'), [
            'name' => 'Ola Operations',
            'email' => 'ola.operations@example.test',
            'job_title' => 'Operations Lead',
            'role_id' => $role->id,
            'property_ids' => [$property->id],
        ])->assertRedirect(route('owner.team.index'));

        $member = User::query()->where('email', 'ola.operations@example.test')->firstOrFail();
        $membership = BusinessMembership::query()->where('business_id', $business->id)->where('user_id', $member->id)->firstOrFail();
        $this->assertDatabaseHas('user_roles', ['user_id' => $member->id, 'role_id' => $role->id, 'business_membership_id' => $membership->id, 'status' => 'active']);
        $this->assertDatabaseHas('property_staff_assignments', ['property_id' => $property->id, 'employee_id' => $membership->employee->id, 'assignment_status' => 'active']);

        $this->delete(route('owner.team.deactivate', $membership))->assertRedirect(route('owner.team.index'));
        $this->assertSame('inactive', $membership->fresh()->status->value);
        $this->assertDatabaseHas('user_roles', ['business_membership_id' => $membership->id, 'status' => 'revoked']);
        $this->assertDatabaseHas('user_business_contexts', ['business_membership_id' => $membership->id, 'status' => 'inactive']);
    }

    public function test_user_can_switch_only_between_their_active_business_memberships(): void
    {
        [$user, $first] = $this->ownerWithBusiness('First Business');
        $second = app(BusinessOnboardingService::class)->onboard($user, $this->businessAttributes('Second Business'));
        $firstMembership = $first->memberships()->where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)->patch(route('owner.business-context.update'), [
            'membership_id' => $firstMembership->id,
        ])->assertRedirect(route('owner.dashboard'));
        $this->assertDatabaseHas('user_business_contexts', [
            'user_id' => $user->id,
            'business_id' => $first->id,
            'business_membership_id' => $firstMembership->id,
            'status' => 'active',
        ]);

        [$outsider, $third] = $this->ownerWithBusiness('Third Business');
        $foreignMembership = $third->memberships()->where('user_id', $outsider->id)->firstOrFail();
        $this->actingAs($user)->patch(route('owner.business-context.update'), [
            'membership_id' => $foreignMembership->id,
        ])->assertNotFound();

        $this->assertNotSame($second->id, $first->id);
    }

    public function test_task_only_staff_see_only_tasks_assigned_to_them(): void
    {
        [, $business] = $this->ownerWithBusiness('Operations Scope Stays');
        $property = Property::factory()->for($business)->create();
        [$cleaner, , $employee] = $this->staffWithRole($business, 'cleaner', [$property]);
        [, , $otherEmployee] = $this->staffWithRole($business, 'cleaner', [$property]);

        $ownTask = OperationalTask::query()->create($this->taskAttributes($business, $property, $employee, 'TASK-MINE', 'My assigned clean'));
        $otherTask = OperationalTask::query()->create($this->taskAttributes($business, $property, $otherEmployee, 'TASK-OTHER', 'Someone else clean'));

        $this->actingAs($cleaner)->get(route('staff.tasks.index'))
            ->assertOk()
            ->assertSee('My assigned clean')
            ->assertDontSee('Someone else clean');
        $this->get(route('owner.entry'))->assertRedirect(route('staff.tasks.index'));
        $this->get(route('owner.operations'))
            ->assertOk()
            ->assertSee('My assigned clean')
            ->assertDontSee('Someone else clean');
        $this->get(route('task-attachments.show', [$otherTask, Str::uuid()]))->assertForbidden();
        $this->get(route('task-attachments.show', [$ownTask, Str::uuid()]))->assertNotFound();
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, $this->businessAttributes($name));

        return [$user, $business];
    }

    /** @return array{User, BusinessMembership, Employee} */
    private function staffWithRole(Business $business, string $roleKey, array $properties = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $membership = BusinessMembership::query()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'job_title' => str($roleKey)->replace('_', ' ')->title(),
            'status' => 'active',
            'joined_at' => now(),
        ]);
        $role = Role::query()->where('system_key', $roleKey)->firstOrFail();
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
        $employee = Employee::query()->create([
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'employee_code' => 'EMP-'.str()->upper(str()->random(8)),
            'employment_status' => 'active',
            'started_on' => today(),
            'status' => 'active',
        ]);

        foreach ($properties as $property) {
            PropertyStaffAssignment::query()->create([
                'business_id' => $business->id,
                'property_id' => $property->id,
                'employee_id' => $employee->id,
                'assignment_role' => $roleKey === 'cleaner' ? 'cleaner' : 'property_manager',
                'assignment_status' => 'active',
                'status' => 'active',
            ]);
        }

        return [$user, $membership, $employee];
    }

    private function override(BusinessMembership $membership, string $permissionKey, string $effect, User $owner): void
    {
        MembershipPermissionOverride::query()->create([
            'business_id' => $membership->business_id,
            'business_membership_id' => $membership->id,
            'permission_id' => Permission::query()->where('key', $permissionKey)->valueOrFail('id'),
            'effect' => $effect,
            'reason' => 'Feature-test override',
            'approved_by' => $owner->id,
            'status' => 'active',
        ]);
    }

    /** @return array<string, string> */
    private function businessAttributes(string $name): array
    {
        return [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ];
    }

    /** @return array<string, mixed> */
    private function taskAttributes(Business $business, Property $property, Employee $employee, string $reference, string $title): array
    {
        return [
            'business_id' => $business->id,
            'property_id' => $property->id,
            'assigned_employee_id' => $employee->id,
            'reference' => $reference,
            'title' => $title,
            'task_type' => 'cleaning',
            'priority' => 'normal',
            'status' => 'assigned',
            'generation_source' => 'manual',
            'due_at' => now()->addDay(),
        ];
    }
}
