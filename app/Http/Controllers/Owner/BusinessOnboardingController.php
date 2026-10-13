<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CompleteBusinessIdentityRequest;
use App\Http\Requests\Owner\StoreBusinessOnboardingRequest;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Identity\DojahIdentityVerifier;
use App\Support\ActiveBusinessContext;
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
        $attributes = $request->businessAttributes();
        $attributes['verification_payload'] = $request->boolean('skip_identity')
            ? ['nin_status' => 'not_submitted']
            : $this->verifyNin($request->string('nin')->toString(), $identity);
        $service->onboard($request->user(), $attributes);

        $message = $request->boolean('skip_identity')
            ? 'Business workspace created.'
            : 'Business profile created. You can now add your first property.';

        return redirect()->route('owner.dashboard')->with('status', $message);
    }

    public function identity(ActiveBusinessContext $context): View|RedirectResponse
    {
        if (data_get($context->business->verification_payload, 'nin_status') === 'verified') {
            return redirect()->route('owner.dashboard')->with('status', 'The business administrator NIN is already verified.');
        }

        return view('owner.onboarding.identity', ['business' => $context->business]);
    }

    public function storeIdentity(CompleteBusinessIdentityRequest $request, ActiveBusinessContext $context, DojahIdentityVerifier $identity): RedirectResponse
    {
        $context->business->update([
            'verification_payload' => array_merge(
                $context->business->verification_payload ?? [],
                $this->verifyNin($request->string('nin')->toString(), $identity),
            ),
        ]);

        return redirect()->route('owner.dashboard')->with('status', 'Administrator NIN verified. Your business can now be submitted for platform approval.');
    }

    /** @return array<string, string|null> */
    private function verifyNin(string $number, DojahIdentityVerifier $identity): array
    {
        try {
            $nin = $identity->verifyNin($number);
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['identity' => $exception->getMessage()]);
        }

        return [
            'nin_status' => 'verified',
            'nin_last4' => substr($number, -4),
            'nin_provider' => $nin['provider'] ?? null,
            'nin_provider_reference' => $nin['reference'] ?? null,
            'identity_environment' => $nin['environment'] ?? null,
            'identity_consent_at' => now()->toIso8601String(),
            'identity_verified_at' => now()->toIso8601String(),
        ];
    }
}
