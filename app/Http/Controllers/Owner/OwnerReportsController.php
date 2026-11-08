<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Finance\OwnerFinanceWorkspace;
use App\Support\ActiveBusinessContext;
use App\Models\TaxCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerReportsController extends Controller
{
    public function reports(Request $request, ActiveBusinessContext $context, OwnerFinanceWorkspace $finance, BusinessPermissionService $permissions): View
    {
        $business = $context->business;
        $filters = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);
        $propertyIds = $permissions->permittedPropertyIds($context);
        $data = $finance->build($business, $filters, $propertyIds);
        $properties = $business->properties()->when($propertyIds !== null, fn ($q) => $q->whereIn('id', $propertyIds))->withCount(['bookings'])->get(['id', 'name', 'tax_enabled', 'tax_rate']);
        return view('owner.reports', compact('business', 'data', 'filters', 'properties'));
    }

    public function statements(Request $request, ActiveBusinessContext $context, OwnerFinanceWorkspace $finance, BusinessPermissionService $permissions): View
    {
        $filters = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);
        $data = $finance->build($context->business, $filters, $permissions->permittedPropertyIds($context));
        return view('owner.statements', ['business' => $context->business, 'data' => $data, 'filters' => $filters]);
    }

    public function tax(ActiveBusinessContext $context, BusinessPermissionService $permissions): View
    {
        $propertyIds = $permissions->permittedPropertyIds($context);
        $properties = $context->business->properties()->when($propertyIds !== null, fn ($q) => $q->whereIn('id', $propertyIds))->get(['id', 'name', 'tax_enabled', 'tax_rate']);
        $categories = TaxCategory::query()->where('business_id', $context->business->id)->where('status', 'active')->orderBy('name')->get();
        return view('owner.tax-compliance', ['business' => $context->business, 'properties' => $properties, 'categories' => $categories]);
    }
}
