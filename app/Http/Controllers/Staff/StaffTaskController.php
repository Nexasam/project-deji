<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OperationalTask;
use App\Services\Operations\BookingOperationsService;
use App\Services\Operations\ManageOperationalTask;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffTaskController extends Controller
{
    public function index(Request $request, ActiveBusinessContext $context): View
    {
        $employee = $context->membership->employee()->where('employment_status', 'active')->firstOrFail();
        $tasks = $context->business->operationalTasks()->where('assigned_employee_id', $employee->id)
            ->with(['property', 'booking', 'checklistItems', 'attachments'])
            ->orderByRaw("CASE WHEN due_at < CURRENT_TIMESTAMP AND status NOT IN ('completed','cancelled') THEN 0 ELSE 1 END")
            ->orderBy('due_at')->paginate(20);

        return view('staff.tasks.index', compact('tasks', 'employee'));
    }

    public function transition(Request $request, ActiveBusinessContext $context, ManageOperationalTask $service, string $task): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:start,complete'],
            'completion_notes' => ['nullable', 'required_if:action,complete', 'string', 'max:2000'],
            'checklist_items' => ['nullable', 'array'],
            'checklist_items.*' => ['uuid'],
            'evidence' => ['nullable', 'array', 'max:8'],
            'evidence.*' => ['image', 'max:8192'],
        ]);
        $employee = $context->membership->employee()->where('employment_status', 'active')->firstOrFail();
        $record = $context->business->operationalTasks()->where('assigned_employee_id', $employee->id)->findOrFail($task);
        $this->authorizeRoleForTask($context, $record);
        $service->transition($record, $request->user(), $data['action'], $data['completion_notes'] ?? null, $data['checklist_items'] ?? [], $request->file('evidence', []));

        return redirect()->route('staff.tasks.index')->with('status', 'Task updated.');
    }

    public function inspection(Request $request, ActiveBusinessContext $context, BookingOperationsService $service, string $task): RedirectResponse
    {
        $data = $request->validate([
            'result' => ['required', 'in:passed,failed'],
            'findings' => ['nullable', 'required_if:result,failed', 'string', 'max:3000'],
            'recommendations' => ['nullable', 'string', 'max:3000'],
            'corrective_action' => ['nullable', 'required_if:result,failed', 'in:cleaning,maintenance'],
            'evidence' => ['nullable', 'array', 'max:8'],
            'evidence.*' => ['image', 'max:8192'],
        ]);
        $employee = $context->membership->employee()->where('employment_status', 'active')->firstOrFail();
        $record = $context->business->operationalTasks()->where('assigned_employee_id', $employee->id)->findOrFail($task);
        $this->authorizeRoleForTask($context, $record);
        $data['evidence'] = $request->file('evidence', []);
        $service->completeInspection($record, $request->user(), $data);

        return redirect()->route('staff.tasks.index')->with('status', 'Inspection recorded.');
    }

    private function authorizeRoleForTask(ActiveBusinessContext $context, OperationalTask $task): void
    {
        $role = $context->roleAssignment->role->system_key;
        $allowed = [
            'cleaner' => ['cleaning', 'deep_cleaning'],
            'maintenance_technician' => ['maintenance', 'repair'],
            'inspector' => ['inspection'],
            'operations_manager' => ['cleaning', 'deep_cleaning', 'maintenance', 'repair', 'inspection', 'inventory_check', 'guest_welcome', 'photography'],
        ];
        abort_unless(in_array($task->task_type->value, $allowed[$role] ?? [], true), 403);
    }
}
