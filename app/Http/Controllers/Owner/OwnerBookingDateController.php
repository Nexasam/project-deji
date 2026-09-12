<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UpdateBookingDatesRequest;
use App\Services\Booking\RescheduleBooking;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;

class OwnerBookingDateController extends Controller
{
    public function update(UpdateBookingDatesRequest $request, ActiveBusinessContext $context, RescheduleBooking $reschedule, string $booking): RedirectResponse
    {
        $booking = $context->business->bookings()->findOrFail($booking);
        $reschedule->handle($booking, $request->user(), $request->validated());

        return redirect()->route('owner.bookings.show', $booking)->with('status', 'Booking dates updated.');
    }
}
