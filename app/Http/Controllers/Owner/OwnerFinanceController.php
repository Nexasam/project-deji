<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\ActiveBusinessContext;
use App\Services\Finance\OwnerFinanceWorkspace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerFinanceController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerFinanceWorkspace $workspace): View
    {
        $business = $context->business;
        $filters = $request->validate(['property' => ['nullable', 'uuid'], 'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from'], 'type' => ['nullable', 'in:payment,refund,expense'], 'q' => ['nullable', 'string', 'max:100']]);

        return view('owner.finance', $workspace->build($business, $filters) + [
            'business' => $business, 'filters' => $filters,
            'properties' => $business->properties()->orderBy('name')->get(['id', 'name']),
            'bookings' => $business->bookings()->with('property')->whereNotIn('status', ['cancelled', 'refunded'])->latest()->limit(100)->get(),
        ]);
    }
}
