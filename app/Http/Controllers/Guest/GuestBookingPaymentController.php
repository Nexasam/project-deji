<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\PayBookingBalance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestBookingPaymentController extends Controller
{
    public function payBalance(Request $request, string $booking, PayBookingBalance $service): RedirectResponse
    {
        $validated = $request->validate([
            'payment_provider' => ['required', 'in:paystack,flutterwave'],
        ]);

        $booking = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->findOrFail($booking);

        $service->handle($booking, $request->user(), $validated['payment_provider']);

        return redirect()
            ->route('guest.bookings.show', $booking)
            ->with('status', 'Balance payment received. Your booking is now fully paid.');
    }
}
