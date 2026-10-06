<?php

namespace App\Services\Platform;

use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PlatformSettings
{
    public function __construct(private readonly PlatformAudit $audit) {}

    /** @var array<string, array{group:string,label:string,description:string,type:string,default:mixed}> */
    public const DEFINITIONS = [
        'reviews.auto_publish_verified' => ['group' => 'reviews', 'label' => 'Auto-publish verified-stay reviews', 'description' => 'Publish reviews immediately when they belong to a completed platform booking.', 'type' => 'boolean', 'default' => true],
        'reviews.invitation_window_days' => ['group' => 'reviews', 'label' => 'Review invitation window', 'description' => 'Number of days a guest has to submit a verified-stay review.', 'type' => 'integer', 'default' => 30],
        'bookings.free_cancellation_hours' => ['group' => 'bookings', 'label' => 'Free cancellation notice', 'description' => 'Minimum hours before check-in required for an automatic full refund.', 'type' => 'integer', 'default' => 48],
        'payments.gateway_execution_mode' => ['group' => 'payments', 'label' => 'Gateway execution mode', 'description' => 'Use simulated for demos. Live-ready validates platform keys before accepting card payments; hosted checkout/webhooks are activated in the real-payment release step.', 'type' => 'string', 'default' => 'simulated'],
        'payments.active_provider' => ['group' => 'payments', 'label' => 'Default payment provider', 'description' => 'Provider shown first on checkout. Guests can still choose another enabled provider.', 'type' => 'string', 'default' => 'paystack'],
        'payments.paystack_enabled' => ['group' => 'payments', 'label' => 'Enable Paystack', 'description' => 'Allow guests to choose Paystack at checkout.', 'type' => 'boolean', 'default' => true],
        'payments.paystack_public_key' => ['group' => 'payments', 'label' => 'Paystack public key', 'description' => 'Verified Shortlet platform Paystack public key.', 'type' => 'string', 'default' => ''],
        'payments.paystack_secret_key' => ['group' => 'payments', 'label' => 'Paystack secret key', 'description' => 'Verified Shortlet platform Paystack secret key. Stored for server-side payment verification.', 'type' => 'secret', 'default' => ''],
        'payments.paystack_webhook_secret' => ['group' => 'payments', 'label' => 'Paystack webhook secret', 'description' => 'Secret used to verify Paystack webhook payloads.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_enabled' => ['group' => 'payments', 'label' => 'Enable Flutterwave', 'description' => 'Allow guests to choose Flutterwave at checkout.', 'type' => 'boolean', 'default' => true],
        'payments.flutterwave_public_key' => ['group' => 'payments', 'label' => 'Flutterwave public key', 'description' => 'Verified Shortlet platform Flutterwave public key.', 'type' => 'string', 'default' => ''],
        'payments.flutterwave_secret_key' => ['group' => 'payments', 'label' => 'Flutterwave secret key', 'description' => 'Verified Shortlet platform Flutterwave secret key. Stored for server-side payment verification.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_encryption_key' => ['group' => 'payments', 'label' => 'Flutterwave encryption key', 'description' => 'Optional Flutterwave encryption key for hosted payment payloads.', 'type' => 'secret', 'default' => ''],
        'payments.flutterwave_webhook_secret' => ['group' => 'payments', 'label' => 'Flutterwave webhook secret', 'description' => 'Secret hash used to verify Flutterwave webhook payloads.', 'type' => 'secret', 'default' => ''],
        'payments.installments_enabled' => ['group' => 'payments', 'label' => 'Allow installment payments', 'description' => 'Allow guests to reserve with a deposit while requiring full payment before check-in.', 'type' => 'boolean', 'default' => true],
        'payments.deposit_percentage' => ['group' => 'payments', 'label' => 'Installment deposit percentage', 'description' => 'Percentage charged immediately when a guest chooses part payment.', 'type' => 'integer', 'default' => 50],
        'payments.balance_due_hours_before_checkin' => ['group' => 'payments', 'label' => 'Balance due before check-in', 'description' => 'Hours before check-in when the remaining balance must be fully paid.', 'type' => 'integer', 'default' => 24],
        'payments.fee_charged_to_guest' => ['group' => 'payments', 'label' => 'Charge platform fee to guest', 'description' => 'When enabled, platform fees are added to the guest total. When disabled, the fee is deducted from owner settlement.', 'type' => 'boolean', 'default' => false],
        'payments.platform_fee_percentage' => ['group' => 'payments', 'label' => 'Platform fee percentage', 'description' => 'Percentage fee recorded for Verified Shortlet on successful bookings.', 'type' => 'integer', 'default' => 5],
        'notifications.email_delivery_enabled' => ['group' => 'notifications', 'label' => 'Queue notification emails', 'description' => 'In-app notifications remain active when email delivery is disabled.', 'type' => 'boolean', 'default' => true],
        'calendar.stale_after_minutes' => ['group' => 'calendar', 'label' => 'Calendar stale threshold', 'description' => 'Minutes without a successful synchronization before an alert is raised.', 'type' => 'integer', 'default' => 45],
        'marketplace.require_verified_business' => ['group' => 'marketplace', 'label' => 'Require verified businesses', 'description' => 'Only publish inventory belonging to businesses verified by the platform.', 'type' => 'boolean', 'default' => false],
        'support.contact_email' => ['group' => 'support', 'label' => 'Platform support email', 'description' => 'Public operational support contact.', 'type' => 'string', 'default' => 'support@verifiedshortlet.test'],
    ];

    public function get(string $key, mixed $fallback = null): mixed
    {
        $definition = self::DEFINITIONS[$key] ?? null;
        $record = PlatformSetting::query()->where('key', $key)->first();

        return $record?->value ?? $definition['default'] ?? $fallback;
    }

    public function all(): Collection
    {
        $saved = PlatformSetting::query()->with('updater')->get()->keyBy('key');

        return collect(self::DEFINITIONS)->map(function (array $definition, string $key) use ($saved): array {
            $record = $saved->get($key);

            return $definition + [
                'key' => $key,
                'value' => $record?->value ?? $definition['default'],
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
                $castValue = $this->cast($value, $definition['type']);
                $record = PlatformSetting::query()->where('key', $key)->lockForUpdate()->first();
                $before = $record?->value ?? $definition['default'];
                if ($record && $record->value === $castValue) {
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
                $this->audit->record($actor, 'platform.setting.updated', $record, "Updated {$definition['label']}.", ['value' => $before], ['value' => $castValue], ['reason' => trim($reason), 'key' => $key]);
            }
        });
    }

    private function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            default => (string) $value,
        };
    }
}
