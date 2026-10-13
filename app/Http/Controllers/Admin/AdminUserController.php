<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\User;
use App\Services\Maintenance\PurgeNonTestWorkData;
use App\Services\Platform\PlatformAudit;
use App\Services\Platform\ManageUserSecurity;
use App\Services\PlatformPermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly PurgeNonTestWorkData $purge,
        private readonly PlatformPermissionService $platformPermissions,
        private readonly PlatformAudit $audit,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'access' => ['nullable', 'in:locked,platform,business,guest'],
        ]);

        $users = User::query()->with(['roleAssignments.role', 'businessMemberships.business'])
            ->withCount(['bookings', 'businessMemberships'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone_number', 'like', "%{$term}%")))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when(($filters['access'] ?? null) === 'locked', fn ($query) => $query->where('locked_until', '>', now()))
            ->when(($filters['access'] ?? null) === 'platform', fn ($query) => $query->whereHas('roleAssignments.role', fn ($role) => $role->where('scope', 'platform')))
            ->when(($filters['access'] ?? null) === 'business', fn ($query) => $query->has('businessMemberships'))
            ->when(($filters['access'] ?? null) === 'guest', fn ($query) => $query->doesntHave('businessMemberships')->whereDoesntHave('roleAssignments.role', fn ($role) => $role->where('scope', 'platform')))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function show(User $user): View
    {
        $user->load([
            'roleAssignments.role',
            'businessMemberships.business',
            'businessMemberships.roles.role',
            'bookings.property',
        ])->loadCount(['bookings', 'businessMemberships']);

        $canPurge = auth()->user()?->hasActiveGlobalRole('platform_super_admin')
            && auth()->id() !== $user->id
            && ! $this->platformPermissions->isPlatformAdministrator($user);

        return view('admin.users.show', [
            'user' => $user,
            'audits' => AuditEvent::query()->with('actor')->where('auditable_type', User::class)->where('auditable_id', $user->id)->latest('occurred_at')->limit(15)->get(),
            'canPurge' => $canPurge,
            'purgePreview' => $canPurge ? $this->purge->previewUser($user) : null,
        ]);
    }

    public function lock(Request $request, User $user, ManageUserSecurity $security): RedirectResponse
    {
        $security->lock($user, $request->user(), $this->reason($request));

        return redirect()->route('admin.users.show', $user)->with('status', 'Account locked and all database sessions terminated.');
    }

    public function unlock(Request $request, User $user, ManageUserSecurity $security): RedirectResponse
    {
        $security->unlock($user, $request->user(), $this->reason($request));

        return redirect()->route('admin.users.show', $user)->with('status', 'Account access restored.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            throw ValidationException::withMessages(['user' => 'You cannot purge your own platform account.']);
        }

        if ($this->platformPermissions->isPlatformAdministrator($user)) {
            throw ValidationException::withMessages(['user' => 'Platform administrator records cannot be purged from this screen.']);
        }

        $phrase = 'DELETE USER '.$user->email;
        $request->validate([
            'confirm' => ['accepted'],
            'confirmation_phrase' => ['required', 'string', Rule::in([$phrase])],
        ]);

        $target = ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
        $result = $this->purge->executeUser($user);

        $this->audit->record(
            $request->user(),
            'platform.user.purged',
            $request->user(),
            'Purged a user and their connected testing records.',
            [],
            [],
            ['target' => $target, 'deleted_records' => $result['deleted'], 'counts' => $result['counts']]
        );

        return redirect()->route('admin.users.index')
            ->with('status', "{$target['email']} and {$result['deleted']} connected record(s) were permanently removed.");
    }

    private function reason(Request $request): string
    {
        return $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000']])['reason'];
    }
}
