<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Finance\OwnerFinanceWorkspace;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerFinanceController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerFinanceWorkspace $workspace, BusinessPermissionService $permissions): View
    {
        $business = $context->business;
        $filters = $request->validate(['property' => ['nullable', 'uuid'], 'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from'], 'type' => ['nullable', 'in:payment,refund,expense'], 'q' => ['nullable', 'string', 'max:100']]);

        $propertyIds = $permissions->permittedPropertyIds($context);

        return view('owner.finance', $workspace->build($business, $filters, $propertyIds) + [
            'business' => $business, 'filters' => $filters,
            'properties' => $business->properties()->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds))->orderBy('name')->get(['id', 'name']),
            'bookings' => $business->bookings()->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))->with('property')->whereNotIn('status', ['cancelled', 'refunded'])->latest()->limit(100)->get(),
        ]);
    }
}
