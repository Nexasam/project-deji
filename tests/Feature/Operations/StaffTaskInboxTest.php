<?php

namespace Tests\Feature\Operations;

use App\Models\Booking;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffTaskInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_sees_and_updates_only_assigned_tasks(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        [$staff, $employee] = $this->staff($business, 'cleaner');
        $property = Property::factory()->for($business)->create(['name' => 'Assigned Suite']);
        $assigned = OperationalTask::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'assigned_employee_id' => $employee->id,
            'reference' => 'TASK-ASSIGNED',
            'title' => 'Turnover clean',
            'task_type' => 'cleaning',
            'priority' => 'urgent',
            'status' => 'assigned',
            'generation_source' => 'manual',
        ]);
        OperationalTask::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'reference' => 'TASK-PRIVATE',
            'title' => 'Unrelated owner task',
            'task_type' => 'cleaning',
            'priority' => 'normal',
            'status' => 'pending',
            'generation_source' => 'manual',
        ]);

        $this->actingAs($staff)->get(route('staff.tasks.index'))
            ->assertOk()->assertSee('Turnover clean')->assertDontSee('Unrelated owner task');
        $this->actingAs($staff)->patch(route('staff.tasks.transition', $assigned), ['action' => 'start'])
            ->assertRedirect(route('staff.tasks.index'));
        $this->assertSame('in_progress', $assigned->fresh()->status->value);
        $this->actingAs($staff)->get(route('owner.finance'))->assertForbidden();
        $this->actingAs($owner)->get(route('owner.finance'))->assertOk();
    }

    public function test_staff_cannot_update_unassigned_or_wrong_role_task(): void
    {
        [, $business] = $this->ownerWithBusiness();
        [$cleaner] = $this->staff($business, 'cleaner');
        $property = Property::factory()->for($business)->create();
        $unassigned = OperationalTask::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'reference' => 'TASK-NOT-MINE',
            'title' => 'Repair boiler',
            'task_type' => 'maintenance',
            'priority' => 'high',
            'status' => 'assigned',
            'generation_source' => 'manual',
        ]);

        $this->actingAs($cleaner)->patch(route('staff.tasks.transition', $unassigned), ['action' => 'start'])->assertNotFound();
    }

    public function test_staff_can_attach_private_completion_evidence_to_assigned_task(): void
    {
        Storage::fake('local');
        [$owner, $business] = $this->ownerWithBusiness();
        [$staff, $employee] = $this->staff($business, 'cleaner');
        $property = Property::factory()->for($business)->create();
        $task = OperationalTask::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'assigned_employee_id' => $employee->id,
            'reference' => 'TASK-EVIDENCE',
            'title' => 'Clean apartment',
            'task_type' => 'cleaning',
            'priority' => 'high',
            'status' => 'in_progress',
            'generation_source' => 'manual',
        ]);

        $this->actingAs($staff)->patch(route('staff.tasks.transition', $task), [
            'action' => 'complete',
            'completion_notes' => 'Cleaned and photographed.',
            'evidence' => [UploadedFile::fake()->image('after.jpg')],
        ])->assertRedirect(route('staff.tasks.index'));

        $attachment = $task->attachments()->sole();
        $this->assertSame('local', $attachment->disk);
        $this->assertSame('Cleaned and photographed.', $task->fresh()->verification_notes);
        Storage::disk('local')->assertExists($attachment->path);
        $this->actingAs($staff)->get(route('task-attachments.show', [$task, $attachment]))->assertOk();
        $this->actingAs($owner)->get(route('owner.operations'))->assertOk()->assertSee('after.jpg');
    }

    public function test_assigned_inspector_can_record_a_passing_inspection_with_private_evidence(): void
    {
        Storage::fake('local');
        [, $business] = $this->ownerWithBusiness();
        [$staff, $employee] = $this->staff($business, 'inspector');
        $property = Property::factory()->for($business)->create(['operational_status' => 'inspection']);
        $booking = Booking::factory()->for($business)->for($property)->create(['status' => 'checked_out']);
        $task = OperationalTask::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'booking_id' => $booking->id, 'assigned_employee_id' => $employee->id,
            'reference' => 'TASK-STAFF-INSPECT', 'title' => 'Inspect cleaned apartment',
            'task_type' => 'inspection', 'priority' => 'high', 'status' => 'in_progress',
            'generation_source' => 'system',
        ]);

        $this->actingAs($staff)->post(route('staff.tasks.inspection', $task), [
            'result' => 'passed',
            'findings' => 'Apartment is guest-ready.',
            'evidence' => [UploadedFile::fake()->image('inspection.jpg')],
        ])->assertRedirect(route('staff.tasks.index'));

        $this->assertSame('completed', $booking->fresh()->status->value);
        $this->assertSame('available', $property->fresh()->operational_status->value);
        $this->assertDatabaseHas('operational_task_attachments', [
            'operational_task_id' => $task->id, 'attachment_type' => 'inspection_evidence', 'disk' => 'local',
        ]);
    }

    private function ownerWithBusiness(): array
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => 'Nexa Stays', 'country_code' => 'NG', 'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
        ]);

        return [$owner, $business];
    }

    private function staff($business, string $roleKey): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $membership = BusinessMembership::query()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'job_title' => str($roleKey)->replace('_', ' ')->title(),
            'status' => 'active',
            'joined_at' => now(),
        ]);
        $assignment = UserRole::query()->create([
            'user_id' => $user->id,
            'role_id' => Role::query()->where('system_key', $roleKey)->value('id'),
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
            'employee_code' => 'EMP-'.strtoupper(substr($user->id, 0, 8)),
            'employment_status' => 'active',
            'started_on' => today(),
            'status' => 'active',
        ]);

        return [$user, $employee];
    }
}
