<?php

namespace App\Services\Platform;

use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

final class PlatformSettings
{
    public function __construct(private readonly PlatformAudit $audit) {}

    /** @var array<string, array{group:string,label:string,description:string,type:string,default:mixed}> */
    public const DEFINITIONS = [
        'reviews.auto_publish_verified' => ['group' => 'reviews', 'label' => 'Auto-publish verified-stay reviews', 'description' => 'Reserved for future policy changes. Go-live reviews require platform moderation.', 'type' => 'boolean', 'default' => false],
        'reviews.invitation_window_days' => ['group' => 'reviews', 'label' => 'Review invitation window', 'description' => 'Number of days a guest has to submit a verified-stay review.', 'type' => 'integer', 'default' => 30],
        'reviews.questionnaire_version' => ['group' => 'reviews', 'label' => 'Scoring questionnaire version', 'description' => 'Increase this before changing scoring weights so historical scores retain their original version.', 'type' => 'integer', 'default' => 1],
        'reviews.cleanliness_weight' => ['group' => 'reviews', 'label' => 'Cleanliness score weight', 'description' => 'Relative percentage used when calculating the property score.', 'type' => 'integer', 'default' => 20],
        'reviews.communication_weight' => ['group' => 'reviews', 'label' => 'Communication score weight', 'description' => 'Relative percentage used when calculating the property score.', 'type' => 'integer', 'default' => 20],
        'reviews.location_weight' => ['group' => 'reviews', 'label' => 'Location score weight', 'description' => 'Relative percentage used when calculating the property score.', 'type' => 'integer', 'default' => 20],
        'reviews.value_weight' => ['group' => 'reviews', 'label' => 'Value score weight', 'description' => 'Relative percentage used when calculating the property score.', 'type' => 'integer', 'default' => 20],
        'reviews.accuracy_weight' => ['group' => 'reviews', 'label' => 'Accuracy score weight', 'description' => 'Relative percentage used when calculating the property score.', 'type' => 'integer', 'default' => 20],
        'bookings.free_cancellation_hours' => ['group' => 'bookings', 'label' => 'Free cancellation notice', 'description' => 'Minimum hours before check-in required for an automatic full refund.', 'type' => 'integer', 'default' => 48],
        'payments.gateway_execution_mode' => ['group' => 'payments', 'label' => 'Payment environment', 'description' => 'Test mode uses provider test credentials. Live mode uses production credentials and real transactions.', 'type' => 'string', 'default' => 'test'],
        'payments.internal_test_checkout_enabled' => ['group' => 'payments', 'label' => 'Allow internal test checkout', 'description' => 'When Test mode is active and provider test credentials are unavailable, allow a non-financial checkout for controlled staging verification. This is always ignored in Live mode.', 'type' => 'boolean', 'default' => true],
        'payments.active_provider' => ['group' => 'payments', 'label' => 'Default payment provider', 'description' => 'Provider shown first on checkout. Guests can still choose another enabled provider.', 'type' => 'string', 'default' => 'paystack'],
        'payments.paystack_enabled' => ['group' => 'payments', 'label' => 'Enable Paystack', 'description' => 'Allow guests to choose Paystack at checkout.', 'type' => 'boolean', 'default' => true],
        'payments.paystack_test_public_key' => ['group' => 'payments', 'label' => 'Paystack test public key', 'description' => 'Public key used only while the payment environment is Test mode.', 'type' => 'string', 'default' => ''],
        'payments.paystack_test_secret_key' => ['group' => 'payments', 'label' => 'Paystack test secret key', 'description' => 'Server-side key used only while the payment environment is Test mode.', 'type' => 'secret', 'default' => ''],
        'payments.paystack_test_webhook_secret' => ['group' => 'payments', 'label' => 'Paystack test webhook secret', 'description' => 'Deprecated. Paystack signs webhooks with the active secret API key.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.paystack_live_public_key' => ['group' => 'payments', 'label' => 'Paystack live public key', 'description' => 'Public key used only while the payment environment is Live mode.', 'type' => 'string', 'default' => ''],
        'payments.paystack_live_secret_key' => ['group' => 'payments', 'label' => 'Paystack live secret key', 'description' => 'Server-side key used only while the payment environment is Live mode.', 'type' => 'secret', 'default' => ''],
        'payments.paystack_live_webhook_secret' => ['group' => 'payments', 'label' => 'Paystack live webhook secret', 'description' => 'Deprecated. Paystack signs webhooks with the active secret API key.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.paystack_public_key' => ['group' => 'payments', 'label' => 'Paystack public key', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'string', 'default' => '', 'hidden' => true],
        'payments.paystack_secret_key' => ['group' => 'payments', 'label' => 'Paystack secret key', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.paystack_webhook_secret' => ['group' => 'payments', 'label' => 'Paystack webhook secret', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.flutterwave_enabled' => ['group' => 'payments', 'label' => 'Enable Flutterwave', 'description' => 'Allow guests to choose Flutterwave at checkout.', 'type' => 'boolean', 'default' => true],
        'payments.flutterwave_test_public_key' => ['group' => 'payments', 'label' => 'Flutterwave test public key', 'description' => 'Public key used only while the payment environment is Test mode.', 'type' => 'string', 'default' => ''],
        'payments.flutterwave_test_secret_key' => ['group' => 'payments', 'label' => 'Flutterwave test secret key', 'description' => 'Server-side key used only while the payment environment is Test mode.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_test_webhook_secret' => ['group' => 'payments', 'label' => 'Flutterwave test webhook secret', 'description' => 'Webhook hash used only while the payment environment is Test mode.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_live_public_key' => ['group' => 'payments', 'label' => 'Flutterwave live public key', 'description' => 'Public key used only while the payment environment is Live mode.', 'type' => 'string', 'default' => ''],
        'payments.flutterwave_live_secret_key' => ['group' => 'payments', 'label' => 'Flutterwave live secret key', 'description' => 'Server-side key used only while the payment environment is Live mode.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_live_webhook_secret' => ['group' => 'payments', 'label' => 'Flutterwave live webhook secret', 'description' => 'Webhook hash used only while the payment environment is Live mode.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_public_key' => ['group' => 'payments', 'label' => 'Flutterwave public key', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'string', 'default' => '', 'hidden' => true],
        'payments.flutterwave_secret_key' => ['group' => 'payments', 'label' => 'Flutterwave secret key', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.flutterwave_encryption_key' => ['group' => 'payments', 'label' => 'Flutterwave encryption key', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.flutterwave_webhook_secret' => ['group' => 'payments', 'label' => 'Flutterwave webhook secret', 'description' => 'Legacy credential retained for existing installations.', 'type' => 'secret', 'default' => '', 'hidden' => true],
        'payments.installments_enabled' => ['group' => 'payments', 'label' => 'Allow installment payments', 'description' => 'Allow guests to reserve with a deposit while requiring full payment before check-in.', 'type' => 'boolean', 'default' => true],
        'payments.deposit_percentage' => ['group' => 'payments', 'label' => 'Installment deposit percentage', 'description' => 'Percentage charged immediately when a guest chooses part payment.', 'type' => 'integer', 'default' => 50],
        'payments.balance_due_hours_before_checkin' => ['group' => 'payments', 'label' => 'Balance due before check-in', 'description' => 'Hours before check-in when the remaining balance must be fully paid.', 'type' => 'integer', 'default' => 24],
        'payments.fee_charged_to_guest' => ['group' => 'payments', 'label' => 'Charge platform fee to guest', 'description' => 'Legacy setting retained for compatibility. The approved policy deducts platform fees from owner settlement.', 'type' => 'boolean', 'default' => false, 'hidden' => true],
        'payments.platform_fee_percentage' => ['group' => 'payments', 'label' => 'Platform fee percentage', 'description' => 'Percentage fee recorded for Verified Shortlet on successful bookings.', 'type' => 'integer', 'default' => 5],
        'payments.platform_fee_fixed' => ['group' => 'payments', 'label' => 'Fixed platform fee', 'description' => 'Fixed fee in the booking currency, combined with the percentage fee.', 'type' => 'decimal', 'default' => 0],
        'payments.partial_refund_penalty_percentage' => ['group' => 'payments', 'label' => 'Part-payment cancellation penalty', 'description' => 'Percentage deducted from captured part payments when an eligible guest cancellation is refunded.', 'type' => 'decimal', 'default' => 10],
        'notifications.email_delivery_enabled' => ['group' => 'notifications', 'label' => 'Queue notification emails', 'description' => 'In-app notifications remain active when email delivery is disabled.', 'type' => 'boolean', 'default' => true],
        'notifications.mail_provider' => ['group' => 'notifications', 'label' => 'Email provider', 'description' => 'Select Gmail, Brevo, or Custom SMTP. Provider presets apply the correct relay host and recommended security settings.', 'type' => 'string', 'default' => 'custom'],
        'notifications.mail_transport' => ['group' => 'notifications', 'label' => 'Email transport', 'description' => 'SMTP works with most providers. API transports require their Laravel transport package.', 'type' => 'string', 'default' => 'smtp'],
        'notifications.mail_host' => ['group' => 'notifications', 'label' => 'SMTP host', 'description' => 'Outgoing mail server hostname.', 'type' => 'string', 'default' => ''],
        'notifications.mail_port' => ['group' => 'notifications', 'label' => 'SMTP port', 'description' => 'Usually 587 for TLS or 465 for SSL.', 'type' => 'integer', 'default' => 587],
        'notifications.mail_scheme' => ['group' => 'notifications', 'label' => 'SMTP security', 'description' => 'Use SMTP/STARTTLS with port 587 or SMTPS/implicit TLS with port 465.', 'type' => 'string', 'default' => 'smtp'],
        'notifications.mail_username' => ['group' => 'notifications', 'label' => 'SMTP username', 'description' => 'Username supplied by the email provider.', 'type' => 'string', 'default' => ''],
        'notifications.mail_password' => ['group' => 'notifications', 'label' => 'SMTP password', 'description' => 'Password or API token supplied by the email provider.', 'type' => 'secret', 'default' => ''],
        'notifications.mail_from_address' => ['group' => 'notifications', 'label' => 'From email address', 'description' => 'Verified sender address shown to recipients.', 'type' => 'string', 'default' => ''],
        'notifications.mail_from_name' => ['group' => 'notifications', 'label' => 'From name', 'description' => 'Sender name shown to recipients.', 'type' => 'string', 'default' => 'Verified Shortlet'],
        'calendar.sync_enabled' => ['group' => 'calendar', 'label' => 'Automatic calendar synchronization', 'description' => 'Poll active external iCalendar feeds automatically.', 'type' => 'boolean', 'default' => true],
        'calendar.sync_interval_minutes' => ['group' => 'calendar', 'label' => 'Calendar synchronization interval', 'description' => 'Minimum minutes between automatic imports for each connection.', 'type' => 'integer', 'default' => 15],
        'calendar.stale_after_minutes' => ['group' => 'calendar', 'label' => 'Calendar stale threshold', 'description' => 'Minutes without a successful synchronization before an alert is raised.', 'type' => 'integer', 'default' => 45],
        'identity.environment' => ['group' => 'identity', 'label' => 'Identity verification environment', 'description' => 'Test uses the provider sandbox; live verifies real NIN and BVN records.', 'type' => 'string', 'default' => 'test'],
        'identity.provider' => ['group' => 'identity', 'label' => 'Identity provider', 'description' => 'Provider used for Nigerian NIN and BVN verification.', 'type' => 'string', 'default' => 'dojah'],
        'identity.dojah_app_id' => ['group' => 'identity', 'label' => 'Dojah App ID', 'description' => 'Application identifier from the Dojah dashboard.', 'type' => 'secret', 'default' => ''],
        'identity.dojah_private_key' => ['group' => 'identity', 'label' => 'Dojah private key', 'description' => 'Private API key used only by the server.', 'type' => 'secret', 'default' => ''],
        'storage.media_driver' => ['group' => 'storage', 'label' => 'Property media storage', 'description' => 'Local public storage or an S3-compatible object store such as DigitalOcean Spaces.', 'type' => 'string', 'default' => 'local'],
        'storage.s3_key' => ['group' => 'storage', 'label' => 'Object storage access key', 'description' => 'Access key for the S3-compatible media bucket.', 'type' => 'secret', 'default' => ''],
        'storage.s3_secret' => ['group' => 'storage', 'label' => 'Object storage secret', 'description' => 'Secret for the S3-compatible media bucket.', 'type' => 'secret', 'default' => ''],
        'storage.s3_region' => ['group' => 'storage', 'label' => 'Object storage region', 'description' => 'For example nyc3, lon1, or us-east-1.', 'type' => 'string', 'default' => 'us-east-1'],
        'storage.s3_bucket' => ['group' => 'storage', 'label' => 'Object storage bucket', 'description' => 'Bucket or Space name used for property media.', 'type' => 'string', 'default' => ''],
        'storage.s3_endpoint' => ['group' => 'storage', 'label' => 'Object storage endpoint', 'description' => 'S3-compatible endpoint; leave blank for AWS S3.', 'type' => 'string', 'default' => ''],
        'storage.s3_url' => ['group' => 'storage', 'label' => 'Public media URL', 'description' => 'Optional CDN or public bucket URL.', 'type' => 'string', 'default' => ''],
        'storage.s3_path_style' => ['group' => 'storage', 'label' => 'Use path-style endpoint', 'description' => 'Enable only when required by the selected S3-compatible provider.', 'type' => 'boolean', 'default' => false],
        'ai.enabled' => ['group' => 'ai', 'label' => 'Enable AI stay concierge', 'description' => 'Answer marketplace questions using published Verified Shortlet listing data.', 'type' => 'boolean', 'default' => false],
        'ai.openai_api_key' => ['group' => 'ai', 'label' => 'OpenAI API key', 'description' => 'Server-side API key. It is encrypted and never exposed to browsers.', 'type' => 'secret', 'default' => ''],
        'ai.openai_model' => ['group' => 'ai', 'label' => 'OpenAI model', 'description' => 'Model used for grounded marketplace concierge answers.', 'type' => 'string', 'default' => 'gpt-5-mini'],
        'marketplace.require_verified_business' => ['group' => 'marketplace', 'label' => 'Require verified businesses', 'description' => 'Only publish inventory belonging to businesses verified by the platform.', 'type' => 'boolean', 'default' => false],
        'support.contact_email' => ['group' => 'support', 'label' => 'Platform support email', 'description' => 'Public operational support contact.', 'type' => 'string', 'default' => 'support@verifiedshortlet.test'],
    ];

    public function get(string $key, mixed $fallback = null): mixed
    {
        $definition = self::DEFINITIONS[$key] ?? null;
        $record = PlatformSetting::query()->where('key', $key)->first();

        $value = $record?->value ?? $definition['default'] ?? $fallback;

        return $definition && $definition['type'] === 'secret' ? $this->decryptSecret($value) : $value;
    }

    public function paymentCredential(string $provider, string $credential): string
    {
        $mode = $this->get('payments.gateway_execution_mode', 'test') === 'live' ? 'live' : 'test';
        $scoped = trim((string) $this->get("payments.{$provider}_{$mode}_{$credential}", ''));

        return $scoped !== ''
            ? $scoped
            : trim((string) $this->get("payments.{$provider}_{$credential}", ''));
    }

    public function all(): Collection
    {
        $saved = PlatformSetting::query()->with('updater')->get()->keyBy('key');

        return collect(self::DEFINITIONS)->reject(fn (array $definition): bool => (bool) ($definition['hidden'] ?? false))->map(function (array $definition, string $key) use ($saved): array {
            $record = $saved->get($key);

            return $definition + [
                'key' => $key,
                'value' => $definition['type'] === 'secret'
                    ? (filled($record?->value) ? 'configured' : '')
                    : ($record?->value ?? $definition['default']),
                'updated_at' => $record?->updated_at,
                'updated_by' => $record?->updater?->name,
            ];
        });
    }

    /** @param array<string, mixed> $values */
    public function setMany(array $values, User $actor, string $reason): void
    {
        DB::transaction(function () use ($values, $actor, $reason): void {
            foreach ($values as $key => $value) {
                $definition = self::DEFINITIONS[$key];
                $plainValue = $this->cast($value, $definition['type']);
                $castValue = $definition['type'] === 'secret' ? Crypt::encryptString($plainValue) : $plainValue;
                $record = PlatformSetting::query()->where('key', $key)->lockForUpdate()->first();
                $before = $record?->value ?? $definition['default'];
                if ($record && ($definition['type'] === 'secret' ? hash_equals((string) $this->decryptSecret($before), (string) $plainValue) : $record->value === $castValue)) {
                    continue;
                }
                $record = PlatformSetting::query()->updateOrCreate(['key' => $key], [
                    'group_key' => $definition['group'],
                    'value' => $castValue,
                    'value_type' => $definition['type'],
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'updated_by' => $actor->id,
                ]);
                $auditedBefore = $definition['type'] === 'secret' ? (filled($before) ? '[configured]' : '[empty]') : $before;
                $auditedAfter = $definition['type'] === 'secret' ? '[configured]' : $castValue;
                $this->audit->record($actor, 'platform.setting.updated', $record, "Updated {$definition['label']}.", ['value' => $auditedBefore], ['value' => $auditedAfter], ['reason' => trim($reason), 'key' => $key]);
            }
        });
    }

    private function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            default => (string) $value,
        };
    }

    private function decryptSecret(mixed $value): string
    {
        if (blank($value)) return '';
        try {
            return Crypt::decryptString((string) $value);
        } catch (DecryptException) {
            return (string) $value;
        }
    }
}
