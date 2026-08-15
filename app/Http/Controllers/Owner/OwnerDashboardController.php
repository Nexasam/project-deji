<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context): View
    {
        if ($request->query('view') === 'demo') {
            return view('owner.demo.dashboard', ['business' => $context->business]);
        }

        $properties = $context->business->properties()
            ->latest()
            ->get(['id', 'name', 'code', 'address', 'property_type', 'publication_status', 'verification_status', 'readiness_status', 'created_at']);

        return view('dashboard', [
            'business' => $context->business,
            'properties' => $properties,
        ]);
    }
}
