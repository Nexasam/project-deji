<?php

namespace App\Http\Controllers;

use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions, string $task, string $attachment): StreamedResponse
    {
        $record = $context->business->operationalTasks()->with('property')->findOrFail($task);
        $canManageTasks = $permissions->allows($request->user(), $context, 'task.assign', $record->property)
            || $permissions->allows($request->user(), $context, 'task.reassign', $record->property);
        $employeeId = $context->membership->employee?->id;
        abort_unless($canManageTasks || ($employeeId && $record->assigned_employee_id === $employeeId), 403);
        $file = $record->attachments()->where('status', 'active')->findOrFail($attachment);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->response($file->path, $file->original_name, [
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $file->original_name ?: 'evidence').'"',
        ]);
    }
}
