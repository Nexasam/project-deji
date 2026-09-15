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
        ]);
        $record = $context->business->bookings()->findOrFail($booking);

        $cancel->handle($request->user(), $record, $data['reason'], 'owner');

        return redirect()->route('owner.bookings.show', $record)
            ->with('status', 'Booking cancelled, dates released, and the guest notified.');
    }
}
