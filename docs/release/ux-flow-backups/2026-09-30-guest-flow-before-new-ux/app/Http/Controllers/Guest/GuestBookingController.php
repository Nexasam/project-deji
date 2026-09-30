<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['property.media', 'property.marketplaceListing'])->latest()->paginate(10);

        return view('guest.bookings.index', compact('bookings'));
    }

    public function show(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['business', 'property.media', 'property.amenities', 'property.marketplaceListing', 'payments', 'cancellations', 'statusHistory', 'serviceRequests.task', 'reviews.response', 'reviewInvitations'])
            ->findOrFail($booking);

        return view('guest.bookings.show', compact('booking'));
    }

    public function receipt(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['business', 'guest', 'property.marketplaceListing', 'payments'])->findOrFail($booking);

        return view('guest.bookings.receipt', compact('booking'));
    }
}
