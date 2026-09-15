<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\BookingDispute;
use App\Models\Business;
use App\Models\Review;
use App\Services\Platform\ManageBusinessStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBusinessController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'verification_status' => ['nullable', 'in:unverified,pending,verified,rejected'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'business_type' => ['nullable', 'string', 'max:80'],
        ]);

        $businesses = Business::query()->withCount(['properties', 'memberships', 'bookings'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('registration_number', 'like', "%{$term}%")))
            ->when($filters['verification_status'] ?? null, fn ($query, $value) => $query->where('verification_status', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['country_code'] ?? null, fn ($query, $value) => $query->where('country_code', strtoupper($value)))
            ->when($filters['business_type'] ?? null, fn ($query, $value) => $query->where('business_type', $value))
            ->latest()->paginate(25)->withQueryString();

        return view('admin.businesses.index', compact('businesses', 'filters'));
    }

    public function show(Business $business): View
    {
        $business->load([
            'memberships.user', 'memberships.roles.role',
            'properties' => fn ($query) => $query->latest()->limit(12),
            'bookings' => fn ($query) => $query->with(['property', 'guest'])->latest()->limit(10),
        ])->loadCount(['properties', 'memberships', 'bookings', 'payments', 'operationalTasks']);

        return view('admin.businesses.show', [
            'business' => $business,
            'reviewCount' => Review::query()->where('business_id', $business->id)->count(),
            'disputeCount' => BookingDispute::query()->where('business_id', $business->id)->count(),
            'audits' => AuditEvent::query()->with('actor')->where('business_id', $business->id)->latest('occurred_at')->limit(12)->get(),
        ]);
    }

    public function verify(Request $request, Business $business, ManageBusinessStatus $manager): RedirectResponse
    {
        $manager->verify($business, $request->user(), $this->reason($request));

        return redirect()->route('admin.businesses.show', $business)->with('status', 'Business verified successfully.');
    }

    public function reject(Request $request, Business $business, ManageBusinessStatus $manager): RedirectResponse
    {
        $manager->reject($business, $request->user(), $this->reason($request));

        return redirect()->route('admin.businesses.show', $business)->with('status', 'Business verification rejected.');
    }

    public function suspend(Request $request, Business $business, ManageBusinessStatus $manager): RedirectResponse
    {
        $manager->suspend($business, $request->user(), $this->reason($request));

        return redirect()->route('admin.businesses.show', $business)->with('status', 'Business suspended. New marketplace and owner mutations are blocked.');
    }

    public function reactivate(Request $request, Business $business, ManageBusinessStatus $manager): RedirectResponse
    {
        $manager->reactivate($business, $request->user(), $this->reason($request));

        return redirect()->route('admin.businesses.show', $business)->with('status', 'Business access and marketplace eligibility restored.');
    }

    private function reason(Request $request): string
    {
        return $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000']])['reason'];
    }
}
