<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Dashboard\OwnerDashboardSummary;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerDashboardSummary $summary, BusinessPermissionService $permissions): View
    {
        if ($request->query('view') === 'demo') {
            return view('owner.demo.dashboard', ['business' => $context->business]);
        }

        $propertyIds = $permissions->permittedPropertyIds($context);
        $properties = $context->business->properties()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds))
            ->with(['media' => fn ($query) => $query
                ->where('status', 'active')
                ->where('media_type', 'image')
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')])
            ->latest()
            ->get(['id', 'name', 'code', 'address', 'property_type', 'publication_status', 'verification_status', 'readiness_status', 'created_at']);

        return view('dashboard', [
            'business' => $context->business,
            'properties' => $properties,
            'summary' => $summary->build($context->business, $request->user(), $propertyIds),
        ]);
    }
}
