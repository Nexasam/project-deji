<?php
namespace App\Services\Operations;
use App\Models\DomainEvent;
use App\Models\Employee;
use App\Models\OperationalTask;
use App\Models\OperationalTaskAssignment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
final class ManageOperationalTask
{
    public function create(Property $property,?Employee $employee,User $owner,array $data): OperationalTask
    {
        return DB::transaction(function()use($property,$employee,$owner,$data){
            $reference='TASK-'.strtoupper(Str::random(10));
            $task=OperationalTask::create(['business_id'=>$property->business_id,'property_id'=>$property->id,'assigned_employee_id'=>$employee?->id,'reference'=>$reference,'title'=>$data['title'],'task_type'=>$data['task_type'],'priority'=>$data['priority'],'status'=>$employee?'assigned':'pending','due_at'=>$data['due_at'],'notes'=>$data['notes']??null,'generation_source'=>'manual','manual_creation_reason'=>'Owner-created operational task','created_by'=>$owner->id,'updated_by'=>$owner->id]);
            if($employee) OperationalTaskAssignment::create(['business_id'=>$property->business_id,'operational_task_id'=>$task->id,'employee_id'=>$employee->id,'assignment_role'=>'primary','assignment_status'=>'assigned','assigned_by'=>$owner->id,'assigned_at'=>now(),'status'=>'active']);
            $this->event($task,'operations.task.created',null,$task->status->value,$owner,['assigned_employee_id'=>$employee?->id]);
            return $task;
        });
    }
    public function transition(OperationalTask $task,User $owner,string $action,?string $notes=null): OperationalTask
    {
        return DB::transaction(function()use($task,$owner,$action,$notes){
            $task=OperationalTask::lockForUpdate()->findOrFail($task->id); $from=$task->status->value;
            $allowed=['start'=>['pending','assigned'],'complete'=>['in_progress']];
            if(!in_array($from,$allowed[$action]??[],true)) throw ValidationException::withMessages(['action'=>'This task transition is not allowed from its current status.']);
            $to=$action==='start'?'in_progress':'completed';
            $values=['status'=>$to,'updated_by'=>$owner->id];
            if($action==='start')$values['started_at']=now(); else $values['completed_at']=now();
            $task->update($values); $this->event($task,"operations.task.{$to}",$from,$to,$owner,['completion_notes'=>$notes]);
            return $task->fresh();
        });
    }
    private function event(OperationalTask $task,string $name,?string $from,string $to,User $owner,array $extra=[]): void
    {
        DomainEvent::create(['business_id'=>$task->business_id,'property_id'=>$task->property_id,'booking_id'=>$task->booking_id,'event_name'=>$name,'aggregate_type'=>OperationalTask::class,'aggregate_id'=>$task->id,'idempotency_key'=>(string)Str::uuid(),'payload'=>compact('from','to')+$extra+['actor_id'=>$owner->id],'occurred_at'=>now(),'publication_status'=>'pending','status'=>'active']);
    }
}
