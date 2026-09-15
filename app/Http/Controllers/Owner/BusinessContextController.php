<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessMembership;
use App\Models\UserBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BusinessContextController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['membership_id' => ['required', 'uuid']]);
        $membership = BusinessMembership::query()->with(['business', 'roles.role'])
            ->where('user_id', $request->user()->id)->where('status', 'active')->findOrFail($data['membership_id']);
        $assignment = $membership->roles->first(fn ($role) => $role->status->value === 'active' && ! $role->revoked_at && (! $role->expires_at || $role->expires_at->isFuture()));
        abort_unless($assignment, 403);

        UserBusinessContext::query()->updateOrCreate(['user_id' => $request->user()->id], [
            'business_id' => $membership->business_id,
            'business_membership_id' => $membership->id,
            'active_user_role_id' => $assignment->id,
            'switched_at' => now(),
            'status' => 'active',
            'updated_by' => $request->user()->id,
        ]);

        $taskOnly = in_array($assignment->role->system_key, ['cleaner', 'maintenance_technician', 'inspector'], true);

        return redirect()->route($taskOnly ? 'staff.tasks.index' : 'owner.dashboard')
            ->with('status', "You are now working in {$membership->business->name}.");
    }
}
