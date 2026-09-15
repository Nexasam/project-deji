<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\PropertyStaffAssignment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use App\Services\Platform\PlatformAudit;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerTeamController extends Controller
{
    public function index(ActiveBusinessContext $context): View
    {
        return view('owner.team.index', [
            'business' => $context->business,
            'memberships' => $context->business->memberships()
                ->with(['user', 'roles.role', 'employee.propertyAssignments.property'])
                ->orderByDesc('joined_at')->get(),
            'roles' => Role::query()->where('scope', 'business')->where('is_template', true)
                ->where('status', 'active')->orderBy('hierarchy_level')->get(),
            'properties' => $context->business->properties()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, ActiveBusinessContext $context, PlatformAudit $audit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'role_id' => ['required', 'uuid'],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['uuid'],
        ]);
        $role = $this->role($data['role_id'], $context);
        $this->validateOwnerAssignment($request, $context, $role);
        $properties = $this->properties($data['property_ids'] ?? [], $context);
        $existingUser = User::query()->where('email', Str::lower($data['email']))->first();

        $membership = DB::transaction(function () use ($data, $role, $properties, $context, $request, $existingUser, $audit): BusinessMembership {
            $user = $existingUser ?: User::query()->create([
                'name' => $data['name'],
                'email' => Str::lower($data['email']),
                'password' => null,
                'timezone' => $context->business->timezone,
                'status' => 'active',
            ]);
            if ($context->business->memberships()->where('user_id', $user->id)->exists()) {
                throw ValidationException::withMessages(['email' => 'This user already belongs to the business.']);
            }

            $membership = $context->business->memberships()->create([
                'user_id' => $user->id,
                'job_title' => $data['job_title'] ?? $role->name,
                'status' => 'active',
                'invited_at' => now(),
                'joined_at' => now(),
            ]);
            $assignment = UserRole::query()->create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'business_membership_id' => $membership->id,
                'status' => 'active',
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            UserBusinessContext::query()->firstOrCreate(['user_id' => $user->id], [
                'business_id' => $context->business->id,
                'business_membership_id' => $membership->id,
                'active_user_role_id' => $assignment->id,
                'switched_at' => now(),
                'status' => 'active',
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $this->syncEmployeeAndProperties($membership, $role, $properties, $request->user());
            $audit->record($request->user(), 'team.member_added', $membership, "{$user->name} was added as {$role->name}.", [], ['role' => $role->system_key, 'properties' => $properties->pluck('id')->all()]);

            return $membership;
        });

        if (! $existingUser) {
            Password::sendResetLink(['email' => Str::lower($data['email'])]);
        }

        return redirect()->route('owner.team.index')->with('status', "{$membership->user->name} was added to the team.");
    }

    public function update(Request $request, ActiveBusinessContext $context, string $membership, PlatformAudit $audit): RedirectResponse
    {
        $record = $context->business->memberships()->with(['user', 'roles', 'employee'])->findOrFail($membership);
        $data = $request->validate([
            'job_title' => ['nullable', 'string', 'max:120'],
            'role_id' => ['required', 'uuid'],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['uuid'],
        ]);
        $role = $this->role($data['role_id'], $context);
        $this->validateOwnerAssignment($request, $context, $role);
        $properties = $this->properties($data['property_ids'] ?? [], $context);

        DB::transaction(function () use ($record, $data, $role, $properties, $request, $audit): void {
            $before = ['job_title' => $record->job_title, 'roles' => $record->roles->pluck('role_id')->all()];
            $record->update(['job_title' => $data['job_title'] ?? $role->name, 'status' => 'active', 'ended_at' => null]);
            $record->roles()->where('status', 'active')->update(['status' => 'revoked', 'revoked_at' => now(), 'updated_by' => $request->user()->id]);
            $assignment = UserRole::query()->updateOrCreate([
                'user_id' => $record->user_id,
                'role_id' => $role->id,
                'scope_key' => $record->id,
            ], [
                'business_membership_id' => $record->id,
                'status' => 'active',
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
                'revoked_at' => null,
                'updated_by' => $request->user()->id,
            ]);
            UserBusinessContext::query()->where('user_id', $record->user_id)
                ->where('business_id', $record->business_id)->update(['active_user_role_id' => $assignment->id, 'status' => 'active', 'updated_by' => $request->user()->id]);
            $this->syncEmployeeAndProperties($record, $role, $properties, $request->user());
            $audit->record($request->user(), 'team.member_role_changed', $record, "{$record->user->name}'s role was changed to {$role->name}.", $before, ['job_title' => $record->job_title, 'role' => $role->system_key, 'properties' => $properties->pluck('id')->all()]);
        });

        return redirect()->route('owner.team.index')->with('status', "{$record->user->name}'s access was updated.");
    }

    public function deactivate(Request $request, ActiveBusinessContext $context, string $membership, PlatformAudit $audit): RedirectResponse
    {
        $record = $context->business->memberships()->with('user')->findOrFail($membership);
        if ($record->user_id === $request->user()->id) {
            throw ValidationException::withMessages(['member' => 'You cannot deactivate your own active membership.']);
        }

        DB::transaction(function () use ($record, $request, $audit): void {
            $record->update(['status' => 'inactive', 'ended_at' => now()]);
            $record->roles()->where('status', 'active')->update(['status' => 'revoked', 'revoked_at' => now(), 'updated_by' => $request->user()->id]);
            $record->employee?->update(['employment_status' => 'suspended', 'status' => 'inactive', 'ended_on' => today()]);
            UserBusinessContext::query()->where('business_membership_id', $record->id)->update(['status' => 'inactive', 'updated_by' => $request->user()->id]);
            $audit->record($request->user(), 'team.member_deactivated', $record, "{$record->user->name} was removed from active business access.", ['status' => 'active'], ['status' => 'inactive']);
        });

        return redirect()->route('owner.team.index')->with('status', "{$record->user->name}'s business access was deactivated.");
    }

    private function role(string $roleId, ActiveBusinessContext $context): Role
    {
        return Role::query()->whereKey($roleId)->where('scope', 'business')->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('business_id')->orWhere('business_id', $context->business->id))
            ->firstOrFail();
    }

    private function properties(array $ids, ActiveBusinessContext $context)
    {
        $records = $context->business->properties()->whereIn('id', $ids)->get();
        if ($records->count() !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['property_ids' => 'One or more selected properties do not belong to this business.']);
        }

        return $records;
    }

    private function validateOwnerAssignment(Request $request, ActiveBusinessContext $context, Role $role): void
    {
        if ($role->system_key === 'business_owner' && ! $request->user()->hasActiveBusinessRole('business_owner', $context->membership->id)) {
            throw ValidationException::withMessages(['role_id' => 'Only a business owner can grant the business-owner role.']);
        }
    }

    private function syncEmployeeAndProperties(BusinessMembership $membership, Role $role, $properties, User $actor): void
    {
        if ($role->system_key === 'business_owner') {
            $membership->employee?->propertyAssignments()->update(['assignment_status' => 'inactive', 'status' => 'inactive', 'updated_by' => $actor->id]);

            return;
        }

        $employee = Employee::query()->updateOrCreate(['business_membership_id' => $membership->id], [
            'business_id' => $membership->business_id,
            'employee_code' => $membership->employee?->employee_code ?: 'EMP-'.Str::upper(Str::random(8)),
            'employment_status' => 'active',
            'started_on' => $membership->employee?->started_on ?: today(),
            'status' => 'active',
        ]);
        $employee->propertyAssignments()->where('assignment_status', 'active')->update(['assignment_status' => 'inactive', 'status' => 'inactive', 'updated_by' => $actor->id]);
        $assignmentRole = match ($role->system_key) {
            'property_manager', 'operations_manager' => 'property_manager',
            'cleaner' => 'cleaner',
            'inspector' => 'inspector',
            'maintenance_technician' => 'maintenance_technician',
            'customer_support' => 'guest_support',
            'accountant' => 'accountant',
            default => 'other',
        };
        foreach ($properties as $property) {
            PropertyStaffAssignment::query()->create([
                'business_id' => $membership->business_id,
                'property_id' => $property->id,
                'employee_id' => $employee->id,
                'assignment_role' => $assignmentRole,
                'assignment_status' => 'active',
                'status' => 'active',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);
        }
    }
}
