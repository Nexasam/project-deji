<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Booking\CancelMarketplaceBooking;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerBookingCancellationController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, CancelMarketplaceBooking $cancel, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
            'keep_dates_blocked' => ['nullable', 'boolean'],
        ]);
        $record = $context->business->bookings()->findOrFail($booking);

        $keepDatesBlocked = $request->boolean('keep_dates_blocked');
        $cancel->handle($request->user(), $record, $data['reason'], 'owner', $keepDatesBlocked);

        return redirect()->route('owner.bookings.show', $record)
            ->with('status', $keepDatesBlocked
                ? 'Booking cancelled, the original dates remain unavailable, and the guest was notified.'
                : 'Booking cancelled, dates released, and the guest was notified.');
    }
}
