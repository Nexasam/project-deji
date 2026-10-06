<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminPlatformSettingController extends Controller
{
    public function index(PlatformSettings $settings): View
    {
        return view('admin.settings.index', ['settings' => $settings->all()->groupBy('group')]);
    }

    public function update(Request $request, PlatformSettings $settings): RedirectResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000']])['reason'];
        $input = Arr::dot($request->input('settings', []));
        $values = [];
        foreach (PlatformSettings::DEFINITIONS as $key => $definition) {
            if ($definition['type'] === 'secret' && ! filled($input[$key] ?? null)) {
                continue;
            }

            $values[$key] = Validator::make(
                ['value' => $input[$key] ?? null],
                ['value' => $this->rules($key, $definition['type'])],
                [],
                ['value' => $definition['label']],
            )->validate()['value'];
        }
        $settings->setMany($values, $request->user(), $reason);

        return redirect()->route('admin.settings.index')->with('status', 'Platform configuration saved and audited.');
    }

    /** @return array<int, string> */
    private function rules(string $key, string $type): array
    {
        return match ($key) {
            'reviews.invitation_window_days' => ['required', 'integer', 'between:1,90'],
            'bookings.free_cancellation_hours' => ['required', 'integer', 'between:0,720'],
            'payments.gateway_execution_mode' => ['required', 'string', 'in:simulated,live_ready'],
            'payments.active_provider' => ['required', 'string', 'in:paystack,flutterwave'],
            'payments.deposit_percentage' => ['required', 'integer', 'between:1,99'],
            'payments.balance_due_hours_before_checkin' => ['required', 'integer', 'between:0,720'],
            'payments.platform_fee_percentage' => ['required', 'integer', 'between:0,50'],
            'calendar.stale_after_minutes' => ['required', 'integer', 'between:5,1440'],
            'support.contact_email' => ['required', 'email', 'max:255'],
            default => match ($type) {
                'boolean' => ['required', 'boolean'],
                'secret' => ['required', 'string', 'max:500'],
                default => ['nullable', 'string', 'max:500'],
            },
        };
    }
}
