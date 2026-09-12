<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Booking\OwnerBookingWorkspace;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerBookingsController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerBookingWorkspace $workspace): View
    {
        $business = $context->business;
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'property' => ['nullable', 'uuid'],
            'status' => ['nullable', 'in:enquiry,reserved,awaiting_payment,confirmed,checked_in,checked_out,completed,cancelled,refunded,no_show'],
            'channel' => ['nullable', 'in:marketplace,airbnb,booking_dot_com,whatsapp,referral,walk_in,corporate,phone,travel_agent,manual'],
            'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from'], 'sort' => ['nullable', 'in:newest,arrival'],
        ]);
        $stats = $workspace->stats($business);
        $bookings = $workspace->paginate($business, $filters);
        $properties = $business->properties()->orderBy('name')->get(['id', 'name']);

        return view('owner.bookings', compact('business', 'stats', 'bookings', 'properties', 'filters'));
    }

    public function show(ActiveBusinessContext $context, OwnerBookingWorkspace $workspace, string $booking): View
    {
        return view('owner.bookings.show', [
            'business' => $context->business,
            'booking' => $workspace->find($context->business, $booking),
        ]);
    }
}
