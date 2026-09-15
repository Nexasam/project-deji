<?php

namespace App\Services\Operations;

use App\Models\Booking;
use App\Models\Inspection;
use App\Models\MaintenanceIssue;
use App\Models\OperationalTask;
use App\Models\OperationalTaskAttachment;
use App\Models\OperationalTaskChecklistItem;
use App\Models\User;
use App\Services\Booking\BookingLifecycleService;
use App\Services\Notifications\ProductNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class BookingOperationsService
{
    public function __construct(private readonly ProductNotificationService $notifications) {}

    public function onBookingConfirmed(Booking $booking, User $actor): void
    {
        $booking->loadMissing(['property.cleaningSchedules', 'property.marketplaceListing']);
        $checkIn = $booking->property->marketplaceListing?->check_in_time ?: '14:00';
        $timezone = $booking->property->business->timezone ?? 'Africa/Lagos';
        $arrival = CarbonImmutable::parse($booking->arrival_date->toDateString().' '.$checkIn, $timezone);
        $schedule = $booking->property->cleaningSchedules->firstWhere('status', 'active') ?? $booking->property->cleaningSchedules->first();
        $preArrivalDue = $schedule?->preferred_start_time
            ? CarbonImmutable::parse($booking->arrival_date->toDateString().' '.$schedule->preferred_start_time, $timezone)
            : $arrival->subHours(3);

        $cleaning = $this->task($booking, 'PREP', [
            'title' => 'Pre-arrival cleaning and readiness', 'task_type' => 'cleaning',
            'priority' => 'high', 'due_at' => $preArrivalDue,
            'notes' => $schedule?->instructions,
            'generation_metadata' => ['workflow_stage' => 'pre_arrival_cleaning'],
        ], $actor);
        $this->checklist($cleaning, ['Clean and sanitise all rooms', 'Replace linen and towels', 'Restock guest essentials', 'Confirm utilities and access are working'], $actor);

        $welcome = $this->task($booking, 'WELCOME', [
            'title' => 'Prepare guest welcome and access', 'task_type' => 'guest_welcome',
            'priority' => 'normal', 'due_at' => $arrival->subHour(),
            'generation_metadata' => ['workflow_stage' => 'guest_welcome'],
        ], $actor);
        $this->checklist($welcome, ['Confirm guest contact details', 'Prepare access handover', 'Send arrival instructions'], $actor);
    }

    public function onBookingCheckedOut(Booking $booking, User $actor): OperationalTask
    {
        $booking->loadMissing(['property.cleaningSchedules']);
        $schedule = $booking->property->cleaningSchedules->firstWhere('status', 'active') ?? $booking->property->cleaningSchedules->first();
        $timezone = $booking->property->business->timezone ?? 'Africa/Lagos';
        $preferredDue = $schedule?->preferred_start_time
            ? CarbonImmutable::parse($booking->departure_date->toDateString().' '.$schedule->preferred_start_time, $timezone)
            : now($timezone)->addHours(4);
        $turnoverDue = $preferredDue->isFuture() ? $preferredDue : now($timezone)->addHours(4);
        $task = $this->task($booking, 'TURNOVER', [
            'title' => 'Turnover cleaning after checkout', 'task_type' => 'cleaning',
            'priority' => 'high', 'due_at' => $turnoverDue,
            'notes' => $schedule?->instructions,
            'generation_metadata' => ['workflow_stage' => 'turnover_cleaning'],
        ], $actor);
        $this->checklist($task, ['Photograph condition before cleaning', 'Clean and sanitise all rooms', 'Replace linen and towels', 'Restock guest essentials', 'Photograph completed property'], $actor);
        $booking->property->update(['operational_status' => 'cleaning', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);

        return $task;
    }

    public function afterTaskCompletion(OperationalTask $task, User $actor): void
    {
        if (! $task->booking_id || data_get($task->generation_metadata, 'workflow_stage') !== 'turnover_cleaning') {
            return;
        }

        $booking = $task->booking()->with('property')->firstOrFail();
        $inspectionTask = $this->task($booking, 'INSPECT', [
            'title' => 'Post-cleaning property inspection', 'task_type' => 'inspection',
            'priority' => 'high', 'due_at' => now()->addHours(2), 'requires_verification' => true,
            'generation_metadata' => ['workflow_stage' => 'post_cleaning_inspection', 'preceding_task_id' => $task->id],
        ], $actor);
        $this->checklist($inspectionTask, ['Verify cleanliness', 'Check inventory and amenities', 'Check for damage', 'Confirm property is guest-ready'], $actor);
        Inspection::query()->firstOrCreate(['business_id' => $booking->business_id, 'operational_task_id' => $inspectionTask->id], [
            'property_id' => $booking->property_id, 'booking_id' => $booking->id,
            'inspection_type' => 'post_cleaning', 'result' => 'pending', 'status' => 'active',
            'created_by' => $actor->id, 'updated_by' => $actor->id,
        ]);
        $booking->property->update(['operational_status' => 'inspection', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);
    }

    public function completeInspection(OperationalTask $task, User $actor, array $data): void
    {
        DB::transaction(function () use ($task, $actor, $data): void {
            $task = OperationalTask::query()->with(['booking.property'])->lockForUpdate()->findOrFail($task->id);
            if ($task->task_type->value !== 'inspection' || ! $task->booking) {
                throw ValidationException::withMessages(['result' => 'This task is not a booking inspection.']);
            }
            if ($task->status->value === 'completed') {
                return;
            }

            $inspection = Inspection::query()->firstOrCreate(['business_id' => $task->business_id, 'operational_task_id' => $task->id], [
                'property_id' => $task->property_id, 'booking_id' => $task->booking_id,
                'inspection_type' => 'post_cleaning', 'result' => 'pending', 'status' => 'active',
                'created_by' => $actor->id, 'updated_by' => $actor->id,
            ]);
            $inspection->update([
                'result' => $data['result'], 'findings' => $data['findings'] ?? null,
                'recommendations' => $data['recommendations'] ?? null,
                'started_at' => $inspection->started_at ?: now(), 'completed_at' => now(),
                'approved_by' => $actor->id, 'approved_at' => now(), 'updated_by' => $actor->id,
            ]);
            $task->update(['status' => 'completed', 'completed_at' => now(), 'verified_by' => $actor->id, 'verified_at' => now(), 'verification_notes' => $data['findings'] ?? null, 'updated_by' => $actor->id]);
            foreach ($data['evidence'] ?? [] as $file) {
                $path = $file->store("operations/{$task->business_id}/tasks/{$task->id}", 'local');
                OperationalTaskAttachment::query()->create([
                    'business_id' => $task->business_id, 'operational_task_id' => $task->id,
                    'attachment_type' => 'inspection_evidence', 'disk' => 'local', 'path' => $path,
                    'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()),
                    'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id,
                ]);
            }

            if ($data['result'] === 'passed') {
                app(BookingLifecycleService::class)->complete($task->booking, $actor, 'Post-cleaning inspection passed');

                return;
            }

            $type = $data['corrective_action'];
            $corrective = $this->task($task->booking, $type === 'maintenance' ? 'MAINT-'.$inspection->id : 'RECLEAN-'.$inspection->id, [
                'title' => $type === 'maintenance' ? 'Correct inspection failure' : 'Repeat cleaning after failed inspection',
                'task_type' => $type === 'maintenance' ? 'maintenance' : 'cleaning',
                'priority' => 'urgent', 'due_at' => now()->addHours(4),
                'notes' => $data['findings'] ?? 'Inspection failed',
                'generation_metadata' => ['workflow_stage' => 'inspection_correction', 'inspection_id' => $inspection->id],
            ], $actor);
            if ($type === 'maintenance') {
                MaintenanceIssue::query()->firstOrCreate(['business_id' => $task->business_id, 'inspection_id' => $inspection->id], [
                    'property_id' => $task->property_id, 'booking_id' => $task->booking_id,
                    'operational_task_id' => $corrective->id, 'reported_by' => $actor->id,
                    'origin' => 'inspection', 'category' => 'general', 'priority' => 'urgent',
                    'description' => $data['findings'] ?? 'Failed inspection requires maintenance',
                    'currency' => $task->booking->currency, 'status' => 'reported',
                    'created_by' => $actor->id, 'updated_by' => $actor->id,
                ]);
            }
            $task->booking->property->update(['operational_status' => $type === 'maintenance' ? 'maintenance' : 'cleaning', 'operational_status_updated_at' => now(), 'updated_by' => $actor->id]);
            $task->loadMissing('business');
            $this->notifications->businessOwners($task->business, 'inspection_failed', 'Property inspection failed', "{$task->booking->property->name} failed inspection. Urgent corrective {$type} work was created.", ['url' => route('owner.operations'), 'booking_id' => $task->booking_id, 'task_id' => $corrective->id]);
        });
    }

    private function task(Booking $booking, string $suffix, array $values, User $actor): OperationalTask
    {
        return OperationalTask::query()->firstOrCreate([
            'business_id' => $booking->business_id, 'reference' => 'TASK-'.$booking->reference.'-'.$suffix,
        ], $values + [
            'property_id' => $booking->property_id, 'booking_id' => $booking->id,
            'status' => 'pending', 'generation_source' => 'system',
            'manual_creation_reason' => null, 'created_by' => $actor->id, 'updated_by' => $actor->id,
        ]);
    }

    private function checklist(OperationalTask $task, array $items, User $actor): void
    {
        foreach ($items as $index => $title) {
            OperationalTaskChecklistItem::query()->firstOrCreate([
                'business_id' => $task->business_id, 'operational_task_id' => $task->id, 'title' => $title,
            ], ['is_required' => true, 'is_completed' => false, 'sort_order' => $index, 'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id]);
        }
    }
}
