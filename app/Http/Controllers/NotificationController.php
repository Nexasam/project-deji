<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $contextRecord = $request->user()->resolvedBusinessContext();
        $activeBusinessContext = $contextRecord
            ? new ActiveBusinessContext($contextRecord->business, $contextRecord->membership, $contextRecord->activeRoleAssignment)
            : null;
        $contextData = [
            'activeBusiness' => $activeBusinessContext?->business,
            'activeBusinessContext' => $activeBusinessContext,
            'availableBusinessMemberships' => $activeBusinessContext
                ? $request->user()->businessMemberships()->with(['business', 'roles.role'])->where('status', 'active')->orderBy('created_at')->get()
                : collect(),
        ];

        if ($request->query('view') === 'demo' && $activeBusinessContext) {
            return view('owner.demo.notifications', $contextData);
        }

        $filters = $request->validate([
            'status' => ['nullable', 'in:all,unread,read'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $status = $filters['status'] ?? 'all';
        $search = trim($filters['q'] ?? '');
        $baseQuery = $request->user()->notifications()->where('status', 'active');
        $notifications = (clone $baseQuery)
            ->when($status === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->when($status === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($search !== '', fn ($query) => $query->where(function ($nested) use ($search): void {
                $nested->where('title', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%');
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'filters' => ['status' => $status, 'q' => $search],
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'unread' => (clone $baseQuery)->whereNull('read_at')->count(),
                'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            ],
        ] + $contextData);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->where('status', 'active')->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'Notifications marked as read.');
    }

    public function read(Request $request, Notification $notification): RedirectResponse
    {
        $ownedNotification = $request->user()->notifications()
            ->where('status', 'active')
            ->whereKey($notification->id)
            ->firstOrFail();

        if ($ownedNotification->read_at === null) {
            $ownedNotification->update(['read_at' => now()]);
        }

        return back()->with('status', 'Notification marked as read.');
    }
}
