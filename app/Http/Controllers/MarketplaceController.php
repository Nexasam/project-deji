<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarketplaceSearchRequest;
use App\Services\Marketplace\MarketplacePropertyQuery;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function __invoke(MarketplaceSearchRequest $request, MarketplacePropertyQuery $marketplace): View
    {
        return view('welcome', ['properties' => $marketplace->paginate($request->validated()), 'filters' => $request->validated()]);
    }
}
