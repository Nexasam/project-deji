<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessRolePermissionSetting;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\Permission;
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
            'roles' => $this->rolesForBusiness($context)
                ->where('status', 'active')->with(['permissions', 'businessPermissionSettings' => fn ($query) => $query->where('business_id', $context->business->id)->where('status', 'active')])->orderBy('hierarchy_level')->get(),
            'properties' => $context->business->properties()->orderBy('name')->get(['id', 'name']),
            'permissionModules' => $this->permissionModules(),
            'roleAccessLevels' => $this->roleAccessLevels($context),
        ]);
    }

    public function show(ActiveBusinessContext $context, string $membership): View
    {
        $record = $context->business->memberships()
            ->with(['user', 'roles.role', 'employee.propertyAssignments.property'])
            ->findOrFail($membership);

        return view('owner.team.show', [
            'business' => $context->business,
            'membership' => $record,
            'roles' => $this->rolesForBusiness($context)
                ->where('status', 'active')->with(['permissions', 'businessPermissionSettings' => fn ($query) => $query->where('business_id', $context->business->id)->where('status', 'active')])->orderBy('hierarchy_level')->get(),
            'properties' => $context->business->properties()->orderBy('name')->get(['id', 'name']),
            'permissionModules' => $this->permissionModules(),
            'roleAccessLevels' => $this->roleAccessLevels($context),
        ]);
    }

    public function storeRole(Request $request, ActiveBusinessContext $context, PlatformAudit $audit): RedirectResponse
    {
        if (! $request->user()->hasActiveBusinessRole('business_owner', $context->membership->id)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = Str::slug($data['name']);
        if ($slug === '') {
            throw ValidationException::withMessages(['name' => 'Enter a valid role name.']);
        }

        $exists = Role::query()
            ->where('scope', 'business')
            ->where(fn ($query) => $query->whereNull('business_id')->orWhere('business_id', $context->business->id))
            ->where('slug', $slug)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['name' => 'A role with this name already exists for this business.']);
        }

        $role = Role::query()->create([
            'business_id' => $context->business->id,
            'name' => $data['name'],
            'slug' => $slug,
            'system_key' => null,
            'scope' => 'business',
            'description' => $data['description'] ?: 'Custom role configured by the property owner.',
            'hierarchy_level' => 95,
            'is_system' => false,
            'is_template' => false,
            'status' => 'active',
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        $audit->record($request->user(), 'team.custom_role_created', $role, "{$role->name} was created for {$context->business->name}.", [], [
            'business_id' => $context->business->id,
            'role_id' => $role->id,
        ]);

        return redirect()->route('owner.team.index')->with('status', "{$role->name} role was created. Set its permissions below before assigning it widely.");
    }

    public function updateRolePermissions(Request $request, ActiveBusinessContext $context, string $role, PlatformAudit $audit): RedirectResponse
    {
        if (! $request->user()->hasActiveBusinessRole('business_owner', $context->membership->id)) {
            abort(403);
        }

        $record = $this->role($role, $context);
        if ($record->system_key === 'business_owner') {
            throw ValidationException::withMessages(['role' => 'The business owner role always has full access and cannot be restricted.']);
        }

        $modules = $this->permissionModules();
        $data = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*' => ['required', 'in:hidden,read,write'],
        ]);

        $permissionKeys = collect($modules)->flatMap(fn ($module) => [...$module['read'], ...$module['write']])->unique()->values();
        $permissions = Permission::query()->whereIn('key', $permissionKeys)->get()->keyBy('key');

        DB::transaction(function () use ($data, $modules, $permissions, $context, $record, $request, $audit): void {
            $before = $this->roleAccessLevels($context)[$record->id] ?? [];

            foreach ($modules as $key => $module) {
                $level = $data['modules'][$key] ?? 'hidden';
                $allowed = match ($level) {
                    'write' => collect([...$module['read'], ...$module['write']])->unique()->values()->all(),
                    'read' => $module['read'],
                    default => [],
                };
                $denied = collect([...$module['read'], ...$module['write']])->diff($allowed)->values()->all();

                foreach ($allowed as $permissionKey) {
                    if (! $permission = $permissions->get($permissionKey)) {
                        continue;
                    }
                    BusinessRolePermissionSetting::query()->updateOrCreate([
                        'business_id' => $context->business->id,
                        'role_id' => $record->id,
                        'permission_id' => $permission->id,
                    ], [
                        'effect' => 'allow',
                        'reason' => "Business owner set {$module['label']} access to {$level}.",
                        'configured_by' => $request->user()->id,
                        'status' => 'active',
                        'updated_by' => $request->user()->id,
                    ]);
                }

                foreach ($denied as $permissionKey) {
                    if (! $permission = $permissions->get($permissionKey)) {
                        continue;
                    }
                    BusinessRolePermissionSetting::query()->updateOrCreate([
                        'business_id' => $context->business->id,
                        'role_id' => $record->id,
                        'permission_id' => $permission->id,
                    ], [
                        'effect' => 'deny',
                        'reason' => "Business owner set {$module['label']} access to {$level}.",
                        'configured_by' => $request->user()->id,
                        'status' => 'active',
                        'updated_by' => $request->user()->id,
                    ]);
                }
            }

            $after = $this->roleAccessLevels($context)[$record->id] ?? [];
            $audit->record($request->user(), 'team.role_permissions_updated', $record, "{$record->name} permissions were updated for {$context->business->name}.", $before, $after);
        });

        return redirect()->route('owner.team.index')->with('status', "{$record->name} permissions were updated.");
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

    public function reactivate(Request $request, ActiveBusinessContext $context, string $membership, PlatformAudit $audit): RedirectResponse
    {
        $record = $context->business->memberships()->with(['user', 'roles', 'employee'])->findOrFail($membership);
        if ($record->status->value === 'active') {
            throw ValidationException::withMessages(['member' => 'This team member is already active.']);
        }

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
            $before = ['status' => $record->status->value, 'roles' => $record->roles->pluck('role_id')->all()];

            $record->update([
                'job_title' => $data['job_title'] ?? $role->name,
                'status' => 'active',
                'joined_at' => $record->joined_at ?: now(),
                'ended_at' => null,
            ]);

            $record->roles()->where('status', 'active')->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'updated_by' => $request->user()->id,
            ]);

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
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            UserBusinessContext::query()->updateOrCreate([
                'user_id' => $record->user_id,
                'business_id' => $record->business_id,
            ], [
                'business_membership_id' => $record->id,
                'active_user_role_id' => $assignment->id,
                'switched_at' => now(),
                'status' => 'active',
                'updated_by' => $request->user()->id,
            ]);

            $this->syncEmployeeAndProperties($record, $role, $properties, $request->user());
            $audit->record($request->user(), 'team.member_reactivated', $record, "{$record->user->name}'s business access was reactivated.", $before, [
                'status' => 'active',
                'role' => $role->system_key ?? $role->slug,
                'properties' => $properties->pluck('id')->all(),
            ]);
        });

        return redirect()->route('owner.team.index')->with('status', "{$record->user->name}'s business access was reactivated.");
    }

    private function role(string $roleId, ActiveBusinessContext $context): Role
    {
        return $this->rolesForBusiness($context)->whereKey($roleId)->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('business_id')->orWhere('business_id', $context->business->id))
            ->firstOrFail();
    }

    private function rolesForBusiness(ActiveBusinessContext $context)
    {
        return Role::query()->where('scope', 'business')
            ->where(fn ($query) => $query
                ->where('is_template', true)
                ->orWhere('business_id', $context->business->id));
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

    private function permissionModules(): array
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard analytics',
                'description' => 'Business dashboard, summary cards and performance indicators.',
                'read' => ['business.view'],
                'write' => [],
                'owner_only_note' => 'Hidden by default for non-owner roles until explicitly allowed.',
            ],
            'properties' => [
                'label' => 'Properties',
                'description' => 'Property records, setup wizard, documents, media, listing submission and review responses.',
                'read' => ['property.view', 'document.download'],
                'write' => ['property.create', 'property.edit', 'property.manage_listing', 'property.manage_documents', 'property.respond_reviews', 'document.upload'],
            ],
            'bookings' => [
                'label' => 'Bookings & messages',
                'description' => 'Reservations, manual bookings, date changes, lifecycle actions, guest messages and guest stay reviews.',
                'read' => ['booking.view_history', 'booking.communicate'],
                'write' => ['booking.create', 'booking.modify', 'booking.cancel', 'booking.check_in', 'booking.check_out', 'booking.manage_guests', 'booking.assign_staff'],
            ],
            'calendar' => [
                'label' => 'Calendar & iCal',
                'description' => 'Availability calendar, manual blocks and external calendar sync.',
                'read' => ['calendar.view'],
                'write' => ['calendar.manage_availability', 'calendar.manage_blocks', 'calendar.manage_connections', 'calendar.sync'],
            ],
            'finance' => [
                'label' => 'Finance',
                'description' => 'Transactions, payments, expenses, profit visibility, reports and reconciliation controls.',
                'read' => ['finance.view_transactions', 'property.view_finance', 'profit.view', 'report.export'],
                'write' => ['payment.record', 'expense.create', 'finance.manage_accounts', 'finance.reconcile', 'finance.manage_revenue', 'finance.manage_expenses', 'finance.approve_expenses', 'refund.process'],
            ],
            'operations' => [
                'label' => 'Operations',
                'description' => 'Cleaning, maintenance, inspection, inventory and operational task workflows.',
                'read' => ['task.view_assigned', 'inventory.view'],
                'write' => ['task.assign', 'task.complete', 'task.verify', 'task.reassign', 'inspection.perform', 'inspection.approve', 'inventory.manage', 'maintenance.create'],
            ],
            'team' => [
                'label' => 'Team administration',
                'description' => 'Invite staff, change roles, deactivate access and configure capabilities.',
                'read' => ['employee.invite'],
                'write' => ['employee.invite', 'employee.change_role', 'employee.remove', 'employee.manage_capabilities'],
            ],
        ];
    }

    private function roleAccessLevels(ActiveBusinessContext $context): array
    {
        $modules = $this->permissionModules();
        $roles = $this->rolesForBusiness($context)
            ->where('status', 'active')
            ->with(['permissions', 'businessPermissionSettings' => fn ($query) => $query->where('business_id', $context->business->id)->where('status', 'active')->with('permission')])
            ->get();

        return $roles->mapWithKeys(function (Role $role) use ($modules) {
            return [$role->id => collect($modules)->mapWithKeys(function (array $module, string $key) use ($role) {
                if ($role->system_key === 'business_owner') {
                    return [$key => 'write'];
                }

                $explicit = $role->businessPermissionSettings
                    ->filter(fn ($setting) => in_array($setting->permission?->key, [...$module['read'], ...$module['write']], true));

                $permissionEffect = fn (string $permissionKey) => $explicit
                    ->first(fn ($setting) => $setting->permission?->key === $permissionKey)?->effect;

                $readKeys = collect($module['read']);
                $writeKeys = collect($module['write']);
                $readAllowed = $readKeys->isNotEmpty() && $readKeys->every(function (string $permissionKey) use ($role, $permissionEffect): bool {
                    $effect = $permissionEffect($permissionKey);
                    if ($effect) {
                        return $effect === 'allow';
                    }

                    if ($permissionKey === 'business.view') {
                        return false;
                    }

                    return $role->permissions->contains('key', $permissionKey);
                });
                $writeAllowed = $writeKeys->isNotEmpty() && $writeKeys->every(function (string $permissionKey) use ($role, $permissionEffect): bool {
                    $effect = $permissionEffect($permissionKey);
                    if ($effect) {
                        return $effect === 'allow';
                    }

                    return $role->permissions->contains('key', $permissionKey);
                });

                return [$key => $writeAllowed ? 'write' : ($readAllowed ? 'read' : 'hidden')];
            })->all()];
        })->all();
    }
}
