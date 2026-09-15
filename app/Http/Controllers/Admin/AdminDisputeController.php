<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\User;
use App\Services\Platform\ManageBookingDispute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDisputeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:open,under_review,awaiting_guest,awaiting_business,resolved,rejected,closed'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'assigned_to' => ['nullable', 'uuid'],
        ]);
        $disputes = BookingDispute::query()->with(['booking.guest', 'property', 'property.business', 'assignee'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('reference', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('booking', fn ($booking) => $booking->where('reference', 'like', "%{$term}%"))))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('dispute_status', $value))
            ->when($filters['priority'] ?? null, fn ($query, $value) => $query->where('priority', $value))
            ->when($filters['assigned_to'] ?? null, fn ($query, $value) => $query->where('assigned_to', $value))
            ->latest('opened_at')->paginate(25)->withQueryString();

        return view('admin.disputes.index', ['disputes' => $disputes, 'filters' => $filters, 'assignees' => $this->assignees()]);
    }

    public function create(Request $request): View
    {
        $bookings = Booking::query()->with(['guest', 'property.business'])->latest()->limit(100)->get();

        return view('admin.disputes.create', ['bookings' => $bookings, 'selectedBooking' => $request->string('booking_id')->toString(), 'assignees' => $this->assignees()]);
    }

    public function store(Request $request, ManageBookingDispute $manager): RedirectResponse
    {
        $data = $request->validate([
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'dispute_type' => ['required', 'in:guest_complaint,property_condition,payment,cancellation,safety,other'],
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'disputed_amount' => ['nullable', 'numeric', 'min:0'],
            'assigned_to' => ['nullable', 'uuid', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'note' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        $dispute = $manager->create(Booking::query()->findOrFail($data['booking_id']), $request->user(), $data);

        return redirect()->route('admin.disputes.show', $dispute)->with('status', 'Dispute case opened. No payment or refund was executed.');
    }

    public function show(BookingDispute $dispute): View
    {
        $dispute->load(['business', 'property', 'booking.guest', 'booking.payments', 'booking.cancellations', 'booking.serviceRequests', 'assignee', 'opener', 'resolver', 'audits.actor']);

        return view('admin.disputes.show', ['dispute' => $dispute, 'assignees' => $this->assignees()]);
    }

    public function assign(Request $request, BookingDispute $dispute, ManageBookingDispute $manager): RedirectResponse
    {
        $data = $request->validate(['assigned_to' => ['required', 'uuid', 'exists:users,id'], 'note' => ['required', 'string', 'min:10', 'max:2000']]);
        $manager->assign($dispute, $request->user(), $data['assigned_to'], $data['note']);

        return redirect()->route('admin.disputes.show', $dispute)->with('status', 'Case assignment updated.');
    }

    public function transition(Request $request, BookingDispute $dispute, ManageBookingDispute $manager): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:under_review,awaiting_guest,awaiting_business,resolved,rejected,closed'],
            'note' => ['required', 'string', 'min:10', 'max:2000'],
            'resolution' => ['nullable', 'string', 'max:4000'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
        ]);
        $manager->transition($dispute, $request->user(), $data);

        return redirect()->route('admin.disputes.show', $dispute)->with('status', 'Dispute status updated. No payment or refund was executed.');
    }

    private function assignees()
    {
        return User::query()->where('status', 'active')->whereHas('roleAssignments', fn ($assignment) => $assignment
            ->where('status', 'active')->whereNull('business_membership_id')->whereHas('role.permissions', fn ($permission) => $permission->where('permissions.key', 'platform.dispute.manage')->where('role_permissions.status', 'active')))
            ->orderBy('name')->get();
    }
}
