<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Calendar\OwnerCalendarWorkspace;
use App\Support\ActiveBusinessContext;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerCalendarController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, OwnerCalendarWorkspace $workspace): View
    {
        $filters = $request->validate(['month' => ['nullable', 'date_format:Y-m'], 'property' => ['nullable', 'uuid']]);
        $month = CarbonImmutable::createFromFormat('!Y-m', $filters['month'] ?? now()->format('Y-m'));

        return view('calendar', $workspace->month($context->business, $month, $filters['property'] ?? null) + [
            'business' => $context->business,
        ]);
    }
}
