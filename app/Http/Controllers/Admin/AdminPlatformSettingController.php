<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Services\Notifications\PlatformMailConfigurator;
use App\Services\Storage\PlatformStorageConfigurator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            if (! array_key_exists($key, $input)) {
                continue;
            }

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

    public function testEmail(Request $request, PlatformMailConfigurator $mail): RedirectResponse
    {
        $data = $request->validate(['recipient' => ['required', 'email', 'max:255']]);
        $mail->apply();
        Mail::raw('Verified Shortlet email delivery is configured successfully.', function ($message) use ($data): void {
            $message->to($data['recipient'])->subject('Verified Shortlet email test');
        });

        return back()->with('status', 'Test email sent to '.$data['recipient'].'.');
    }

    public function testStorage(Request $request, PlatformStorageConfigurator $storage): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);
        $storage->apply();
        $path = 'health-checks/'.Str::uuid().'.txt';
        $contents = 'Verified Shortlet storage check '.now()->toIso8601String();
        Storage::disk('platform_media')->put($path, $contents);
        abort_unless(Storage::disk('platform_media')->get($path) === $contents, 500, 'Storage read verification failed.');
        Storage::disk('platform_media')->delete($path);

        return back()->with('status', 'Media storage write, read and delete checks passed.');
    }

    /** @return array<int, string> */
    private function rules(string $key, string $type): array
    {
        return match ($key) {
            'reviews.invitation_window_days' => ['required', 'integer', 'between:1,90'],
            'reviews.questionnaire_version' => ['required', 'integer', 'between:1,1000'],
            'reviews.cleanliness_weight', 'reviews.communication_weight', 'reviews.location_weight', 'reviews.value_weight', 'reviews.accuracy_weight' => ['required', 'integer', 'between:0,100'],
            'bookings.free_cancellation_hours' => ['required', 'integer', 'between:0,720'],
            'payments.gateway_execution_mode' => ['required', 'string', 'in:test,live'],
            'payments.active_provider' => ['required', 'string', 'in:paystack,flutterwave'],
            'payments.deposit_percentage' => ['required', 'integer', 'between:1,99'],
            'payments.balance_due_hours_before_checkin' => ['required', 'integer', 'between:0,720'],
            'payments.platform_fee_percentage' => ['required', 'integer', 'between:0,50'],
            'payments.platform_fee_fixed' => ['required', 'numeric', 'between:0,999999999'],
            'payments.partial_refund_penalty_percentage' => ['required', 'numeric', 'between:0,100'],
            'notifications.mail_provider' => ['required', 'string', 'in:gmail,brevo,custom'],
            'notifications.mail_transport' => ['required', 'string', 'in:smtp'],
            'notifications.mail_scheme' => ['required', 'string', 'in:smtp,smtps'],
            'notifications.mail_port' => ['required', 'integer', 'between:1,65535'],
            'notifications.mail_from_address' => ['nullable', 'email', 'max:255'],
            'calendar.sync_interval_minutes' => ['required', 'integer', 'between:5,1440'],
            'calendar.stale_after_minutes' => ['required', 'integer', 'between:5,1440'],
            'identity.environment' => ['required', 'string', 'in:test,live'],
            'identity.provider' => ['required', 'string', 'in:dojah'],
            'storage.media_driver' => ['required', 'string', 'in:local,s3'],
            'support.contact_email' => ['required', 'email', 'max:255'],
            default => match ($type) {
                'boolean' => ['required', 'boolean'],
                'secret' => ['required', 'string', 'max:500'],
                default => ['nullable', 'string', 'max:500'],
            },
        };
    }
}
