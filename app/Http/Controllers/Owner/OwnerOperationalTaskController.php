<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreOperationalTaskRequest;
use App\Http\Requests\Owner\TransitionOperationalTaskRequest;
use App\Services\Operations\ManageOperationalTask;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerOperationalTaskController extends Controller
{
    public function store(StoreOperationalTaskRequest $request, ActiveBusinessContext $context, ManageOperationalTask $service): RedirectResponse
    {
        $data = $request->validated();
        $property = $context->business->properties()->findOrFail($data['property_id']);
        $employee = empty($data['assigned_employee_id']) ? null : $context->business->employees()->findOrFail($data['assigned_employee_id']);
        $service->create($property, $employee, $request->user(), $data);

        return redirect()->route('owner.operations')->with('status', 'Task created.');
    }

    public function transition(TransitionOperationalTaskRequest $request, ActiveBusinessContext $context, ManageOperationalTask $service, string $task): RedirectResponse
    {
        $task = $context->business->operationalTasks()->findOrFail($task);
        $data = $request->validated();
        $service->transition($task, $request->user(), $data['action'], $data['completion_notes'] ?? null, $data['checklist_items'] ?? [], $request->file('evidence', []));

        return redirect()->route('owner.operations')->with('status', 'Task updated.');
    }

    public function assign(Request $request, ActiveBusinessContext $context, ManageOperationalTask $service, string $task): RedirectResponse
    {
        $data = $request->validate(['assigned_employee_id' => ['required', 'uuid']]);
        $task = $context->business->operationalTasks()->findOrFail($task);
        $employee = $context->business->employees()->where('employment_status', 'active')->findOrFail($data['assigned_employee_id']);
        $service->assign($task, $employee, $request->user());

        return back()->with('status', 'Task assigned.');
    }

    public function schedule(Request $request, ActiveBusinessContext $context, ManageOperationalTask $service, string $task): RedirectResponse
    {
        $data = $request->validate(['priority' => ['required', 'in:low,normal,high,urgent'], 'due_at' => ['required', 'date']]);
        $record = $context->business->operationalTasks()->findOrFail($task);
        $service->updateSchedule($record, $request->user(), $data);

        return back()->with('status','Task schedule updated.');
    }
}
