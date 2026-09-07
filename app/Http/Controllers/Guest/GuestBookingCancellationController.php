<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\CancelMarketplaceBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestBookingCancellationController extends Controller
{
    public function __invoke(Request $request, string $booking, CancelMarketplaceBooking $cancel): RedirectResponse
    {
        $booking = Booking::where('guest_user_id', $request->user()->id)->findOrFail($booking);
        $cancel->handle($request->user(), $booking, $request->string('reason')->toString());

        return redirect()->route('guest.bookings.show', $booking);
    }
}
