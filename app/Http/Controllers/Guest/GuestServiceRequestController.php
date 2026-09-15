<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\ManageGuestServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestServiceRequestController extends Controller
{
    public function store(Request $request, string $booking, ManageGuestServiceRequest $service): RedirectResponse
    {
        $data = $request->validate([
            'request_type' => ['required', 'in:cleaning,maintenance,amenity,access,noise,other'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        $record = Booking::query()->where('guest_user_id', $request->user()->id)->findOrFail($booking);
        $service->create($record, $request->user(), $data);

        return redirect()->route('guest.bookings.show', $record)->with('status', 'Your request has been sent to the property team.');
    }
}
