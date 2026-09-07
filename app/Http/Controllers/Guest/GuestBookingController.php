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
            ->with('property')->latest()->paginate(10);
        return view('guest.bookings.index', compact('bookings'));
    }

    public function show(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['property', 'payments'])->findOrFail($booking);
        return view('guest.bookings.show', compact('booking'));
    }
}
