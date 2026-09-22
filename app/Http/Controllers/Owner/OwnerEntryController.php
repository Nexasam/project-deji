<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;

class OwnerEntryController extends Controller
{
    public function __invoke(BusinessPermissionService $permissions): RedirectResponse
    {
        $context = auth()->user()?->resolvedBusinessContext();
        if ($context) {
            $activeContext = new ActiveBusinessContext($context->business, $context->membership, $context->activeRoleAssignment);
            $user = auth()->user();
            $taskOnly = in_array($context->activeRoleAssignment->role->system_key, [
                'cleaner',
                'maintenance_technician',
                'inspector',
            ], true);

            if ($taskOnly) {
                return redirect()->route('staff.tasks.index');
            }

            foreach ([
                'owner.dashboard' => 'business.view',
                'owner.properties.index' => 'property.view',
                'owner.bookings' => 'booking.view_history',
                'owner.messages.index' => 'booking.communicate',
                'owner.calendar' => 'calendar.view',
                'owner.finance' => 'finance.view_transactions',
                'owner.operations' => 'task.view_assigned',
                'owner.team.index' => 'employee.invite',
            ] as $route => $permission) {
                if ($permissions->allows($user, $activeContext, $permission)) {
                    return redirect()->route($route);
                }
            }

            abort(403);
        }

        return redirect()->route('owner.dashboard');
    }
}
