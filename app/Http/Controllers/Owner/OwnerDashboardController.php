<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Dashboard\OwnerDashboardSummary;
use App\Support\ActiveBusinessContext;
use App\Support\CompactMoney;
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

        $summaryData = $summary->build($context->business, $request->user(), $propertyIds);
        $currency = $context->business->currency;
        $user = $request->user();
        $hasGuestActivity = $user->bookings()->exists() || $user->favourites()->exists();

        return view('dashboard', [
            'business' => $context->business,
            'properties' => $properties,
            'summary' => $summaryData,
            'hasGuestActivity' => $hasGuestActivity,
            'summaryCards' => [
                ['label' => 'Properties', 'value' => $summaryData['propertyCount']],
                ['label' => 'Published', 'value' => $summaryData['publishedProperties']],
                ['label' => 'Arrivals · 30d', 'value' => $summaryData['upcomingArrivals']],
                ['label' => 'Departures · 30d', 'value' => $summaryData['upcomingDepartures']],
                ['label' => 'Revenue', 'value' => CompactMoney::format($summaryData['receivedRevenue'], $currency), 'exact' => CompactMoney::exact($summaryData['receivedRevenue'], $currency)],
                ['label' => 'Unpaid bookings', 'value' => $summaryData['unpaidBookings']],
                ['label' => 'Open tasks', 'value' => $summaryData['openTasks']],
                ['label' => 'Urgent tasks', 'value' => $summaryData['urgentTasks']],
            ],
        ]);
    }
}
