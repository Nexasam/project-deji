<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\Employee;
use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_creates_assigns_starts_and_completes_task_with_immutable_events(): void
    {
        [$owner,$business]=$this->ownerWithBusiness('Nexa Stays');
        $property=Property::factory()->for($business)->create(['name'=>'Admiralty Suite']);
        $employee=Employee::query()->create(['business_id'=>$business->id,'business_membership_id'=>$business->memberships()->first()->id,'employee_code'=>'EMP-001','employment_status'=>'active','started_on'=>'2026-09-01','status'=>'active']);

        $this->actingAs($owner)->post(route('owner.operations.tasks.store'),['property_id'=>$property->id,'assigned_employee_id'=>$employee->id,'title'=>'Prepare apartment','task_type'=>'cleaning','priority'=>'urgent','due_at'=>'2027-09-11T10:00','notes'=>'Guest arrives at noon'])->assertRedirect(route('owner.operations'));
        $task=OperationalTask::query()->sole();
        $this->assertSame('assigned',$task->status->value);
        $this->assertDatabaseHas('operational_task_assignments',['operational_task_id'=>$task->id,'employee_id'=>$employee->id,'assignment_status'=>'assigned']);

        $this->actingAs($owner)->patch(route('owner.operations.tasks.transition',$task),['action'=>'start'])->assertRedirect(route('owner.operations'));
        $this->assertSame('in_progress',$task->fresh()->status->value);
        $this->actingAs($owner)->patch(route('owner.operations.tasks.transition',$task),['action'=>'complete','completion_notes'=>'Apartment inspected and ready'])->assertRedirect(route('owner.operations'));
        $this->assertSame('completed',$task->fresh()->status->value);
        $this->assertDatabaseCount('domain_events',3);
        $this->assertDatabaseHas('domain_events',['aggregate_id'=>$task->id,'event_name'=>'operations.task.completed']);
    }

    public function test_invalid_transition_and_foreign_property_or_task_are_rejected(): void
    {
        [$owner,$business]=$this->ownerWithBusiness('Nexa Stays');
        [, $other]=$this->ownerWithBusiness('Other Stays');
        $foreignProperty=Property::factory()->for($other)->create();
        $ownProperty=Property::factory()->for($business)->create();
        $task=OperationalTask::query()->create(['business_id'=>$business->id,'property_id'=>$ownProperty->id,'reference'=>'TASK-OWN','title'=>'Pending task','task_type'=>'inspection','priority'=>'normal','status'=>'pending','generation_source'=>'manual']);
        $foreignTask=OperationalTask::query()->create(['business_id'=>$other->id,'property_id'=>$foreignProperty->id,'reference'=>'TASK-FOREIGN','title'=>'Foreign task','task_type'=>'cleaning','priority'=>'normal','status'=>'pending','generation_source'=>'manual']);

        $this->actingAs($owner)->patch(route('owner.operations.tasks.transition',$task),['action'=>'complete','completion_notes'=>'Skip start'])->assertSessionHasErrors('action');
        $this->actingAs($owner)->post(route('owner.operations.tasks.store'),['property_id'=>$foreignProperty->id,'title'=>'Not mine','task_type'=>'cleaning','priority'=>'normal','due_at'=>'2027-09-11T10:00'])->assertNotFound();
        $this->actingAs($owner)->patch(route('owner.operations.tasks.transition',$foreignTask),['action'=>'start'])->assertNotFound();
    }

    public function test_operations_filters_and_dashboard_are_backed_by_real_scoped_records(): void
    {
        [$owner,$business]=$this->ownerWithBusiness('Nexa Stays');
        [, $other]=$this->ownerWithBusiness('Other Stays');
        $property=Property::factory()->for($business)->create(['name'=>'Visible Suite']);
        $foreign=Property::factory()->for($other)->create(['name'=>'Foreign Suite']);
        OperationalTask::query()->create(['business_id'=>$business->id,'property_id'=>$property->id,'reference'=>'TASK-VISIBLE','title'=>'Urgent cleaning','task_type'=>'cleaning','priority'=>'urgent','status'=>'pending','generation_source'=>'manual','due_at'=>'2026-09-11']);
        OperationalTask::query()->create(['business_id'=>$other->id,'property_id'=>$foreign->id,'reference'=>'TASK-FOREIGN','title'=>'Foreign work','task_type'=>'cleaning','priority'=>'urgent','status'=>'pending','generation_source'=>'manual']);

        $this->actingAs($owner)->get(route('owner.operations',['property'=>$property->id,'status'=>'pending','priority'=>'urgent']))->assertOk()->assertSee('Urgent cleaning')->assertDontSee('Foreign work');
        $this->actingAs($owner)->get(route('owner.dashboard'))->assertOk()->assertSee('Open tasks')->assertSee('Urgent tasks')->assertDontSee('Operational dashboard preview');
    }

    private function ownerWithBusiness(string $name): array
    {
        $this->seed(AccessControlSeeder::class); $user=User::factory()->create(['email_verified_at'=>now()]);
        $business=app(BusinessOnboardingService::class)->onboard($user,['name'=>$name,'country_code'=>'NG','business_type'=>'serviced_apartments','timezone'=>'Africa/Lagos','currency'=>'NGN']);
        return [$user,$business];
    }
}
