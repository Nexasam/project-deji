<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreBusinessOnboardingRequest;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Identity\DojahIdentityVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class BusinessOnboardingController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->user()?->resolvedBusinessContext()) {
            return redirect()->route('owner.dashboard');
        }

        return view('owner.onboarding.business');
    }

    public function store(StoreBusinessOnboardingRequest $request, BusinessOnboardingService $service, DojahIdentityVerifier $identity): RedirectResponse
    {
        try {
            $nin = $identity->verifyNin($request->string('nin')->toString());
            $bvn = $identity->verifyBvn($request->string('bvn')->toString());
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['identity' => $exception->getMessage()]);
        }

        $attributes = $request->businessAttributes();
        $attributes['verification_payload'] = [
            'nin_status' => 'verified',
            'nin_last4' => substr($request->string('nin')->toString(), -4),
            'nin_provider' => $nin['provider'] ?? null,
            'nin_provider_reference' => $nin['reference'] ?? null,
            'bvn_status' => 'verified',
            'bvn_last4' => substr($request->string('bvn')->toString(), -4),
            'bvn_provider' => $bvn['provider'] ?? null,
            'bvn_provider_reference' => $bvn['reference'] ?? null,
            'identity_environment' => $nin['environment'] ?? $bvn['environment'] ?? null,
            'identity_consent_at' => now()->toIso8601String(),
            'identity_verified_at' => now()->toIso8601String(),
        ];
        $service->onboard($request->user(), $attributes);

        return redirect()->route('owner.dashboard')->with('status', 'Business profile created. You can now add your first property.');
    }
}
