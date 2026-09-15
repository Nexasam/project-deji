<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Operations\OwnerOperationsWorkspace;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerOperationsController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerOperationsWorkspace $workspace, BusinessPermissionService $permissions): View
    {
        $filters = $request->validate([
            'property' => ['nullable', 'uuid'],
            'status' => ['nullable', 'in:pending,assigned,in_progress,blocked,completed,cancelled'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $business = $context->business;
        $propertyIds = $permissions->permittedPropertyIds($context);
        $canManageAllTasks = $permissions->allows($request->user(), $context, 'task.assign')
            || $permissions->allows($request->user(), $context, 'task.reassign');
        $assignedEmployeeId = $canManageAllTasks
            ? null
            : $context->membership->employee()->where('employment_status', 'active')->value('id');

        abort_if(! $canManageAllTasks && ! $assignedEmployeeId, 403);

        return view('owner.operations', $workspace->build($business, $filters, $propertyIds, $assignedEmployeeId) + [
            'business' => $business,
            'filters' => $filters,
            'properties' => $business->properties()->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds))->orderBy('name')->get(['id', 'name']),
            'employees' => $canManageAllTasks
                ? $business->employees()->with('businessMembership.user')->where('employment_status', 'active')->get()
                : collect(),
        ]);
    }
}
