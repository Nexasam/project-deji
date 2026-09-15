<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingLifecycleService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerBookingLifecycleController extends Controller
{
    public function checkIn(Request $request, ActiveBusinessContext $context, BookingLifecycleService $lifecycle, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'identity_status' => ['required', 'in:verified,waived'],
            'balance_status' => ['required', 'in:paid,approved_outstanding'],
            'security_deposit_status' => ['nullable', 'in:not_required,confirmed,waived'],
            'access_method' => ['nullable', 'string', 'max:40'],
            'access_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $record = $context->business->bookings()->findOrFail($booking);
        $lifecycle->checkIn($record, $request->user(), $data);

        return back()->with('status', 'Guest checked in successfully.');
    }

    public function checkOut(Request $request, ActiveBusinessContext $context, BookingLifecycleService $lifecycle, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'access_return_status' => ['required', 'in:returned,not_applicable,missing'],
            'room_condition' => ['required', 'in:good,requires_cleaning,damage_reported'],
            'damage_status' => ['required', 'in:none,reported'],
            'damage_notes' => ['nullable', 'required_if:damage_status,reported', 'string', 'max:2000'],
            'handover_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $record = $context->business->bookings()->findOrFail($booking);
        $lifecycle->checkOut($record, $request->user(), $data);

        return back()->with('status', 'Guest checked out. Turnover preparation can now begin.');
    }

    public function noShow(Request $request, ActiveBusinessContext $context, BookingLifecycleService $lifecycle, string $booking): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);
        $record = $context->business->bookings()->findOrFail($booking);
        $lifecycle->markNoShow($record, $request->user(), $data['reason'] ?? null);

        return back()->with('status', 'Booking marked as no-show.');
    }
}
