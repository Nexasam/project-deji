<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketplaceBookingRequest;
use App\Http\Requests\QuoteMarketplaceBookingRequest;
use App\Services\Booking\BookingPricingService;
use App\Services\Booking\PropertyAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Services\Booking\CreateMarketplaceBooking;
use App\Services\Marketplace\MarketplacePropertyQuery;
use Illuminate\Http\RedirectResponse;

class MarketplaceCheckoutController extends Controller
{
    public function quote(QuoteMarketplaceBookingRequest $request, string $slug, MarketplacePropertyQuery $marketplace, BookingPricingService $pricing, PropertyAvailabilityService $availability): JsonResponse
    {
        $data = $request->validated();
        $property = $marketplace->eligibleBySlug($slug);
        $guests = (int) $data['adult_count'] + (int) ($data['child_count'] ?? 0);
        if ($guests > $property->capacity) throw ValidationException::withMessages(['adult_count' => 'Guest count exceeds this property capacity.']);
        $arrival = CarbonImmutable::parse($data['arrival_date']);
        $departure = CarbonImmutable::parse($data['departure_date']);
        if (! $availability->isAvailable($property, $arrival, $departure)) throw ValidationException::withMessages(['arrival_date' => 'These dates are no longer available.']);
        $quote = $pricing->quote($property, $arrival, $departure);

        return response()->json([
            'currency' => $quote->currency, 'nights' => $quote->nights,
            'subtotal' => (float) $quote->decimal($quote->subtotalMinor),
            'discount' => (float) $quote->decimal($quote->discountMinor),
            'service_fee' => (float) $quote->decimal($quote->serviceFeeMinor),
            'total' => (float) $quote->decimal($quote->totalMinor),
        ]);
    }

    public function store(StoreMarketplaceBookingRequest $request, string $slug, MarketplacePropertyQuery $marketplace, CreateMarketplaceBooking $creator): RedirectResponse
    {
        $booking = $creator->handle($request->user(), $marketplace->eligibleBySlug($slug), $request->validated());

        return redirect()->route('guest.bookings.show', $booking);
    }
}
