<?php

namespace App\Http\Controllers;

use App\Services\Marketplace\MarketplacePropertyQuery;
use Illuminate\View\View;

class MarketplacePropertyController extends Controller
{
    public function __invoke(string $slug, MarketplacePropertyQuery $marketplace): View
    {
        return view('marketplace.show', ['property' => $marketplace->eligibleBySlug($slug)]);
    }

    public function reviews(string $slug, MarketplacePropertyQuery $marketplace): View
    {
        $property = $marketplace->eligibleBySlug($slug);
        $reviews = $property->reviews;
        $average = round((float) $reviews->avg('rating'), 1);
        $categories = collect([
            'Cleanliness' => 'cleanliness_rating',
            'Accuracy' => 'accuracy_rating',
            'Communication' => 'communication_rating',
            'Location' => 'location_rating',
            'Value' => 'value_rating',
        ])->map(fn (string $field) => round((float) $reviews->whereNotNull($field)->avg($field), 1));
        $distribution = collect(range(5, 1))->mapWithKeys(fn (int $score) => [
            $score => $reviews->isEmpty() ? 0 : round($reviews->where('rating', $score)->count() / $reviews->count() * 100),
        ]);

        return view('marketplace.reviews', compact('property', 'reviews', 'average', 'categories', 'distribution'));
    }
}
