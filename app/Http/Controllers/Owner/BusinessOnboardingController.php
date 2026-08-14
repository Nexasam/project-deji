<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreBusinessOnboardingRequest;
use App\Services\Business\BusinessOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessOnboardingController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->user()->resolvedBusinessContext()) {
            return redirect()->route('owner.dashboard');
        }

        return view('owner.onboarding.business');
    }

    public function store(StoreBusinessOnboardingRequest $request, BusinessOnboardingService $service): RedirectResponse
    {
        $service->onboard($request->user(), $request->businessAttributes());

        return redirect()->route('owner.dashboard')->with('status', 'Business profile created. You can now add your first property.');
    }
}
