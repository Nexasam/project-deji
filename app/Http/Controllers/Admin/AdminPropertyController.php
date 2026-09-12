<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\Property\PropertyPublishingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPropertyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,pending,published,unpublished'],
        ]);
        $properties = Property::query()->with(['business', 'marketplaceListing'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhereHas('business', fn ($business) => $business->where('name', 'like', "%{$term}%"))))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('publication_status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.properties.index', compact('properties', 'filters'));
    }

    public function show(Property $property): View
    {
        return view('admin.properties.show', ['property' => $property->load(['business', 'marketplaceListing', 'media'])]);
    }

    public function publish(Request $request, Property $property, PropertyPublishingService $publisher): RedirectResponse
    {
        $publisher->publish($property, $request->user());

        return redirect()->route('admin.properties.show', $property)->with('status', 'Property published to the marketplace.');
    }

    public function unpublish(Request $request, Property $property, PropertyPublishingService $publisher): RedirectResponse
    {
        $publisher->unpublish($property, $request->user());

        return redirect()->route('admin.properties.show', $property)->with('status', 'Property removed from the marketplace.');
    }
}
