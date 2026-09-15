<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OwnerEntryController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $context = auth()->user()?->resolvedBusinessContext();
        if ($context) {
            $taskOnly = in_array($context->activeRoleAssignment->role->system_key, [
                'cleaner',
                'maintenance_technician',
                'inspector',
            ], true);

            return redirect()->route($taskOnly ? 'staff.tasks.index' : 'owner.dashboard');
        }

        return redirect()->route('owner.dashboard');
    }
}
