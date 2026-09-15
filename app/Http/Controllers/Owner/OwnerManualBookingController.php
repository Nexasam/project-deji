<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Booking\CreateManualBooking;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerManualBookingController extends Controller
{
    public function create(ActiveBusinessContext $context): View
    {
        return view('owner.bookings.create', ['business' => $context->business, 'properties' => $context->business->properties()->where('status', 'active')->orderBy('name')->get()]);
    }

    public function store(Request $request, ActiveBusinessContext $context, CreateManualBooking $creator): RedirectResponse
    {
        $data = $request->validate([
            'property_id' => ['required', 'uuid'], 'source' => ['required', 'in:walk_in,phone,whatsapp,referral,corporate'],
            'guest_name' => ['required', 'string', 'max:255'], 'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:32'], 'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_date' => ['required', 'date', 'after:arrival_date'], 'adult_count' => ['required', 'integer', 'min:1'],
            'child_count' => ['nullable', 'integer', 'min:0'], 'nightly_rate' => ['required', 'numeric', 'min:1'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'], 'payment_method' => ['required_with:amount_paid', 'in:cash,bank_transfer,card'],
            'notes' => ['nullable', 'string', 'max:2000'], 'idempotency_key' => ['required', 'string', 'max:100'],
        ]);
        $property = $context->business->properties()->findOrFail($data['property_id']);
        $booking = $creator->handle($property, $request->user(), $data);

        return redirect()->route('owner.bookings.show', $booking)->with('status', 'Manual booking created and calendar dates reserved.');
    }
}
