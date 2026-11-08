<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerExpensesController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions): View
    {
        $filters = $request->validate([
            'property' => ['nullable', 'uuid'],
            'category' => ['nullable', 'in:cleaning,maintenance,utilities,supplies,staff,commission,other'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $propertyIds = $permissions->permittedPropertyIds($context);
        $expenses = $context->business->expenses()->with('property')
            ->where('status', 'active')
            ->when($propertyIds !== null, fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->when($filters['property'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('incurred_on', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('incurred_on', '<=', $date))
            ->when($filters['q'] ?? null, fn ($q, $search) => $q->where(fn ($query) => $query->where('description', 'like', "%{$search}%")->orWhere('payee', 'like', "%{$search}%")->orWhere('reference', 'like', "%{$search}%")))
            ->latest('incurred_on')->paginate(25)->withQueryString();

        return view('owner.expenses', [
            'business' => $context->business,
            'expenses' => $expenses,
            'filters' => $filters,
            'properties' => $context->business->properties()->when($propertyIds !== null, fn ($q) => $q->whereIn('id', $propertyIds))->orderBy('name')->get(['id', 'name']),
            'currency' => $context->business->currency,
        ]);
    }
}
