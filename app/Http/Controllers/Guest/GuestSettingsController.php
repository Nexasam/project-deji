<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Enums\IdentityVerificationStatus;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\Identity\DojahIdentityVerifier;
use Throwable;
use Illuminate\Validation\ValidationException;

class GuestSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('guest.settings', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request, DojahIdentityVerifier $identity): RedirectResponse
    {
        $data = $request->validated();
        $notificationKeys = ['booking_updates', 'host_messages', 'saved_stay_price_drops', 'promotions'];
        $preferences = collect($notificationKeys)
            ->mapWithKeys(fn (string $key) => [$key => $request->boolean("notifications.{$key}")])
            ->all();

        $request->user()->fill($data + [
            'marketing_preferences' => ['notifications' => $preferences],
        ]);

        if ($request->filled('nin')) {
            $validatedIdentity = $request->validate(['nin' => ['string', 'regex:/^\d{11}$/']]);
            try {
                $result = $identity->verifyNin($validatedIdentity['nin']);
            } catch (Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages(['nin' => $exception->getMessage()]);
            }
            $request->user()->identity_verification_status = IdentityVerificationStatus::Verified;
            $request->user()->identity_verification = $result + ['nin_last4' => substr($validatedIdentity['nin'], -4), 'verified_at' => now()->toIso8601String()];
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('guest.settings.edit')->with('status', $request->filled('nin') ? 'identity-verified' : 'profile-updated');
    }
}
