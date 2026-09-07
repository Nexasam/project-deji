<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketplaceBookingRequest;
use App\Services\Booking\CreateMarketplaceBooking;
use App\Services\Marketplace\MarketplacePropertyQuery;
use Illuminate\Http\RedirectResponse;

class MarketplaceCheckoutController extends Controller
{
    public function store(StoreMarketplaceBookingRequest $request, string $slug, MarketplacePropertyQuery $marketplace, CreateMarketplaceBooking $creator): RedirectResponse
    {
        $booking = $creator->handle($request->user(), $marketplace->eligibleBySlug($slug), $request->validated());

        return redirect()->route('guest.bookings.show', $booking);
    }
}
