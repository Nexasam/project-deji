<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\GuestFavourite;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestFavouriteController extends Controller
{
    public function index(Request $request): View
    {
        $favourites = GuestFavourite::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'property.business',
                'property.marketplaceListing',
                'property.media',
                'property.promotions' => fn ($query) => $query->where('status', 'active')->where('publication_status', 'published'),
            ])
            ->latest('favourited_at')
            ->paginate(12);

        return view('guest.favourites.index', compact('favourites'));
    }

    public function store(Request $request, string $property): JsonResponse|RedirectResponse
    {
        $property = $this->eligibleProperty($property);

        GuestFavourite::query()->updateOrCreate([
            'user_id' => $request->user()->id,
            'property_id' => $property->id,
        ], [
            'favourited_at' => now(),
        ]);

        return $this->response($request, true, 'Saved to favourites.');
    }

    public function destroy(Request $request, string $property): JsonResponse|RedirectResponse
    {
        GuestFavourite::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property)
            ->delete();

        return $this->response($request, false, 'Removed from favourites.');
    }

    private function eligibleProperty(string $property): Property
    {
        return Property::query()
            ->whereKey($property)
            ->where([
                'booking_mode' => 'entire',
                'verification_status' => 'verified',
                'publication_status' => 'published',
                'readiness_status' => 'ready',
                'status' => 'active',
            ])
            ->whereHas('business', fn ($query) => $query->where('status', 'active'))
            ->whereHas('marketplaceListing', fn ($query) => $query->where([
                'publication_status' => 'published',
                'is_publication_eligible' => true,
                'status' => 'active',
            ]))
            ->firstOrFail();
    }

    private function response(Request $request, bool $favourited, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['favourited' => $favourited, 'message' => $message]);
        }

        return back()->with('status', $message);
    }
}
