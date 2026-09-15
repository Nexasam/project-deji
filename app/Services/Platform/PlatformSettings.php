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
        'notifications.email_delivery_enabled' => ['group' => 'notifications', 'label' => 'Queue notification emails', 'description' => 'In-app notifications remain active when email delivery is disabled.', 'type' => 'boolean', 'default' => true],
        'calendar.stale_after_minutes' => ['group' => 'calendar', 'label' => 'Calendar stale threshold', 'description' => 'Minutes without a successful synchronization before an alert is raised.', 'type' => 'integer', 'default' => 45],
        'marketplace.require_verified_business' => ['group' => 'marketplace', 'label' => 'Require verified businesses', 'description' => 'Only publish inventory belonging to businesses verified by the platform.', 'type' => 'boolean', 'default' => false],
        'support.contact_email' => ['group' => 'support', 'label' => 'Platform support email', 'description' => 'Public operational support contact. Provider secrets remain environment-managed.', 'type' => 'string', 'default' => 'support@verifiedshortlet.test'],
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
