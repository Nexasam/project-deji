<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'event' => ['nullable', 'string', 'max:150'],
            'administrator' => ['nullable', 'uuid'],
            'business' => ['nullable', 'uuid'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $events = AuditEvent::query()->with(['actor', 'business'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('description', 'like', "%{$term}%")->orWhere('event_type', 'like', "%{$term}%")))
            ->when($filters['event'] ?? null, fn ($query, $value) => $query->where('event_type', $value))
            ->when($filters['administrator'] ?? null, fn ($query, $value) => $query->where('created_by', $value))
            ->when($filters['business'] ?? null, fn ($query, $value) => $query->where('business_id', $value))
            ->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereDate('occurred_at', '>=', $value))
            ->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereDate('occurred_at', '<=', $value))
            ->latest('occurred_at')->paginate(40)->withQueryString();

        return view('admin.audit.index', [
            'events' => $events,
            'filters' => $filters,
            'eventTypes' => AuditEvent::query()->select('event_type')->distinct()->orderBy('event_type')->pluck('event_type'),
            'administrators' => User::query()->whereHas('roleAssignments.role', fn ($role) => $role->where('scope', 'platform'))->orderBy('name')->get(),
            'businesses' => Business::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
