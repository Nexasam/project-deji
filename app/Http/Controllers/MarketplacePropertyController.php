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
}
