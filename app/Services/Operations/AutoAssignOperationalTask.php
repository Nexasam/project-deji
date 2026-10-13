<?php

namespace App\Services\Operations;

use App\Models\Employee;
use App\Models\OperationalTask;
use App\Models\OperationalTaskAssignment;
use App\Models\User;

final class AutoAssignOperationalTask
{
    public function assign(OperationalTask $task, User $actor): OperationalTask
    {
        if ($task->assigned_employee_id) {
            return $task;
        }

        $roleKeys = match ($task->task_type->value) {
            'cleaning', 'deep_cleaning' => ['cleaner'],
            'maintenance', 'repair' => ['maintenance_technician'],
            'inspection' => ['inspector'],
            'guest_welcome' => ['reception_officer', 'operations_manager', 'property_manager'],
            default => ['operations_manager', 'property_manager'],
        };

        $employee = Employee::query()
            ->where('business_id', $task->business_id)->where('employment_status', 'active')->where('status', 'active')
            ->with(['businessMembership.roles.role.parent', 'propertyAssignments' => fn ($query) => $query->where('assignment_status', 'active')->where('status', 'active')])
            ->withCount(['tasks as open_tasks_count' => fn ($query) => $query->whereIn('status', ['assigned', 'in_progress'])])
            ->get()
            ->filter(function (Employee $employee) use ($task, $roleKeys): bool {
                if (data_get($employee->availability, 'enabled', true) !== true) {
                    return false;
                }
                $activeRole = $employee->businessMembership?->roles->first(fn ($assignment) => $assignment->status->value === 'active' && ! $assignment->revoked_at)?->role;
                $roleKey = $activeRole?->system_key ?? $activeRole?->parent?->system_key;
                $propertyIds = $employee->propertyAssignments->pluck('property_id');

                return in_array($roleKey, $roleKeys, true)
                    && ($propertyIds->isEmpty() || $propertyIds->contains($task->property_id));
            })
            ->sortBy(fn (Employee $employee) => [$employee->open_tasks_count, $employee->employee_code])
            ->first();

        if (! $employee) {
            return $task;
        }

        OperationalTaskAssignment::query()->create([
            'business_id' => $task->business_id, 'operational_task_id' => $task->id,
            'employee_id' => $employee->id, 'assignment_role' => 'primary',
            'assignment_status' => 'assigned', 'assigned_by' => $actor->id,
            'assigned_at' => now(), 'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id,
        ]);
        $task->update(['assigned_employee_id' => $employee->id, 'status' => 'assigned', 'updated_by' => $actor->id]);

        return $task->fresh();
    }
}
