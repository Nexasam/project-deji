<?php

namespace App\Services\Operations;

use App\Models\DomainEvent;
use App\Models\Employee;
use App\Models\OperationalTask;
use App\Models\OperationalTaskAssignment;
use App\Models\OperationalTaskAttachment;
use App\Models\Property;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ManageOperationalTask
{
    public function __construct(private readonly BookingOperationsService $bookingOperations, private readonly ProductNotificationService $notifications) {}

    public function create(Property $property, ?Employee $employee, User $owner, array $data): OperationalTask
    {
        return DB::transaction(function () use ($property, $employee, $owner, $data) {
            $reference = 'TASK-'.strtoupper(Str::random(10));
            $task = OperationalTask::create(['business_id' => $property->business_id, 'property_id' => $property->id, 'assigned_employee_id' => $employee?->id, 'reference' => $reference, 'title' => $data['title'], 'task_type' => $data['task_type'], 'priority' => $data['priority'], 'status' => $employee ? 'assigned' : 'pending', 'due_at' => $data['due_at'], 'notes' => $data['notes'] ?? null, 'generation_source' => 'manual', 'manual_creation_reason' => 'Owner-created operational task', 'created_by' => $owner->id, 'updated_by' => $owner->id]);
            if ($employee) {
                OperationalTaskAssignment::create(['business_id' => $property->business_id, 'operational_task_id' => $task->id, 'employee_id' => $employee->id, 'assignment_role' => 'primary', 'assignment_status' => 'assigned', 'assigned_by' => $owner->id, 'assigned_at' => now(), 'status' => 'active']);
            }
            $this->event($task, 'operations.task.created', null, $task->status->value, $owner, ['assigned_employee_id' => $employee?->id]);

            return $task;
        });
    }

    public function transition(OperationalTask $task, User $owner, string $action, ?string $notes = null, array $completedChecklistIds = [], array $evidence = []): OperationalTask
    {
        return DB::transaction(function () use ($task, $owner, $action, $notes, $completedChecklistIds, $evidence) {
            $task = OperationalTask::with('checklistItems')->lockForUpdate()->findOrFail($task->id);
            $from = $task->status->value;
            $allowed = ['start' => ['pending', 'assigned'], 'complete' => ['in_progress']];
            if (! in_array($from, $allowed[$action] ?? [], true)) {
                throw ValidationException::withMessages(['action' => 'This task transition is not allowed from its current status.']);
            }
            if ($action === 'complete') {
                $task->checklistItems()->whereIn('id', $completedChecklistIds)->update(['is_completed' => true, 'completed_by' => $owner->id, 'completed_at' => now(), 'updated_by' => $owner->id]);
                if ($task->checklistItems()->where('is_required', true)->where('is_completed', false)->exists()) {
                    throw ValidationException::withMessages(['checklist_items' => 'Complete every required checklist item before completing this task.']);
                }
            }
            $to = $action === 'start' ? 'in_progress' : 'completed';
            $values = ['status' => $to, 'updated_by' => $owner->id];
            if ($action === 'start') {
                $values['started_at'] = now();
            } else {
                $values['completed_at'] = now();
                $values['verification_notes'] = $notes;
            }
            $task->update($values);
            $this->event($task, "operations.task.{$to}", $from, $to, $owner, ['completion_notes' => $notes]);
            foreach ($evidence as $file) {
                $path = $file->store("operations/{$task->business_id}/tasks/{$task->id}", 'local');
                OperationalTaskAttachment::query()->create([
                    'business_id' => $task->business_id, 'operational_task_id' => $task->id,
                    'attachment_type' => $action === 'complete' ? 'completion_evidence' : 'general', 'disk' => 'local', 'path' => $path,
                    'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'size_bytes' => $file->getSize(),
                    'checksum' => hash_file('sha256', $file->getRealPath()), 'status' => 'active', 'created_by' => $owner->id, 'updated_by' => $owner->id,
                ]);
            }
            if ($action === 'complete') {
                $this->bookingOperations->afterTaskCompletion($task->fresh(), $owner);
            }

            return $task->fresh();
        });
    }

    public function assign(OperationalTask $task, Employee $employee, User $owner): OperationalTask
    {
        return DB::transaction(function () use ($task, $employee, $owner) {
            $task = OperationalTask::query()->with('property.business')->lockForUpdate()->findOrFail($task->id);
            if ($task->business_id !== $employee->business_id) {
                abort(404);
            }
            if (in_array($task->status->value, ['completed', 'cancelled'], true)) {
                throw ValidationException::withMessages(['assigned_employee_id' => 'Completed or cancelled work cannot be reassigned.']);
            }
            $previous = $task->assigned_employee_id;
            $task->assignments()->where('assignment_status', 'assigned')->update(['assignment_status' => 'released', 'released_at' => now(), 'release_reason' => 'Owner reassigned task', 'updated_by' => $owner->id]);
            OperationalTaskAssignment::query()->create(['business_id' => $task->business_id, 'operational_task_id' => $task->id, 'employee_id' => $employee->id, 'assignment_role' => 'primary', 'assignment_status' => 'assigned', 'assigned_by' => $owner->id, 'assigned_at' => now(), 'status' => 'active', 'created_by' => $owner->id, 'updated_by' => $owner->id]);
            $task->update(['assigned_employee_id' => $employee->id, 'status' => $task->status->value === 'pending' ? 'assigned' : $task->status->value, 'updated_by' => $owner->id]);
            $this->event($task, 'operations.task.assigned', $task->status->value, $task->status->value, $owner, ['previous_employee_id' => $previous, 'assigned_employee_id' => $employee->id]);
            $employee->loadMissing('businessMembership.user');
            if ($employee->businessMembership?->user) {
                $this->notifications->user($employee->businessMembership->user, $task->property->business, 'task_assigned', 'New task assigned', "{$task->title} at {$task->property->name} has been assigned to you.", ['url' => route('staff.tasks.index'), 'task_id' => $task->id]);
            }

            return $task->fresh();
        });
    }

    public function updateSchedule(OperationalTask $task, User $owner, array $data): OperationalTask
    {
        return DB::transaction(function () use ($task, $owner, $data) {
            $task = OperationalTask::query()->lockForUpdate()->findOrFail($task->id);
            if (in_array($task->status->value, ['completed', 'cancelled'], true)) {
                throw ValidationException::withMessages(['due_at' => 'Completed or cancelled work cannot be rescheduled.']);
            }
            $before = ['due_at' => $task->due_at?->toIso8601String(), 'priority' => $task->priority->value];
            $task->update(['due_at' => $data['due_at'], 'priority' => $data['priority'], 'updated_by' => $owner->id]);
            $this->event($task, 'operations.task.rescheduled', $task->status->value, $task->status->value, $owner, ['before' => $before, 'after' => ['due_at' => $task->due_at?->toIso8601String(), 'priority' => $task->priority->value]]);

            return $task->fresh();
        });
    }

    private function event(OperationalTask $task, string $name, ?string $from, string $to, User $owner, array $extra = []): void
    {
        DomainEvent::create(['business_id' => $task->business_id, 'property_id' => $task->property_id, 'booking_id' => $task->booking_id, 'event_name' => $name, 'aggregate_type' => OperationalTask::class, 'aggregate_id' => $task->id, 'idempotency_key' => (string) Str::uuid(), 'payload' => compact('from', 'to') + $extra + ['actor_id' => $owner->id], 'occurred_at' => now(), 'publication_status' => 'pending', 'status' => 'active']);
    }
}
