<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Enums\IdentityVerificationStatus;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class GuestSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('guest.settings', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
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
            $request->validate(['nin' => ['string', 'max:32']]);
            $request->user()->identity_verification_status = IdentityVerificationStatus::Verified;
            $request->user()->identity_verification = [
                'mode' => 'simulated',
                'nin_last4' => substr(preg_replace('/\D/', '', (string) $request->input('nin')), -4),
                'verified_at' => now()->toIso8601String(),
            ];
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('guest.settings.edit')->with('status', 'profile-updated');
    }
}
