<?php

namespace App\Services\Platform;

use App\Models\AuditEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class PlatformAudit
{
    /** @param array<string, mixed> $before
     * @param  array<string, mixed>  $after
     * @param  array<string, mixed>  $metadata
     */
    public function record(User $actor, string $event, Model $subject, string $description, array $before = [], array $after = [], array $metadata = []): AuditEvent
    {
        return AuditEvent::query()->create([
            'business_id' => $subject->getAttribute('business_id') ?: ($subject->getTable() === 'businesses' ? $subject->getKey() : null),
            'auditable_type' => $subject::class,
            'auditable_id' => $subject->getKey(),
            'event_type' => $event,
            'description' => $description,
            'before_values' => $before,
            'after_values' => $after,
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'occurred_at' => now(),
            'status' => 'active',
            'created_by' => $actor->id,
        ]);
    }
}
