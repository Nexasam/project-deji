<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreOperationalTaskRequest;
use App\Http\Requests\Owner\TransitionOperationalTaskRequest;
use App\Services\Operations\ManageOperationalTask;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
class OwnerOperationalTaskController extends Controller
{
    public function store(StoreOperationalTaskRequest $request,ActiveBusinessContext $context,ManageOperationalTask $service): RedirectResponse
    {
        $data=$request->validated(); $property=$context->business->properties()->findOrFail($data['property_id']);
        $employee=empty($data['assigned_employee_id'])?null:$context->business->employees()->findOrFail($data['assigned_employee_id']);
        $service->create($property,$employee,$request->user(),$data);
        return redirect()->route('owner.operations')->with('status','Task created.');
    }
    public function transition(TransitionOperationalTaskRequest $request,ActiveBusinessContext $context,ManageOperationalTask $service,string $task): RedirectResponse
    {
        $task=$context->business->operationalTasks()->findOrFail($task); $data=$request->validated();
        $service->transition($task,$request->user(),$data['action'],$data['completion_notes']??null);
        return redirect()->route('owner.operations')->with('status','Task updated.');
    }
}
