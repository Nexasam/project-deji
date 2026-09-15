<?php

namespace App\Services\Platform;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ManageBusinessStatus
{
    public function __construct(private readonly PlatformAudit $audit) {}

    public function verify(Business $business, User $actor, string $reason): Business
    {
        return $this->transition($business, $actor, $reason, ['verification_status' => 'verified'], 'platform.business.verified', 'Verified business');
    }

    public function reject(Business $business, User $actor, string $reason): Business
    {
        return $this->transition($business, $actor, $reason, ['verification_status' => 'rejected'], 'platform.business.rejected', 'Rejected business verification for');
    }

    public function suspend(Business $business, User $actor, string $reason): Business
    {
        return $this->transition($business, $actor, $reason, ['status' => 'suspended'], 'platform.business.suspended', 'Suspended business');
    }

    public function reactivate(Business $business, User $actor, string $reason): Business
    {
        return $this->transition($business, $actor, $reason, ['status' => 'active'], 'platform.business.reactivated', 'Reactivated business');
    }

    /** @param array<string, string> $changes */
    private function transition(Business $business, User $actor, string $reason, array $changes, string $event, string $description): Business
    {
        if (mb_strlen(trim($reason)) < 10) {
            throw ValidationException::withMessages(['reason' => 'Give a clear reason of at least 10 characters.']);
        }

        return DB::transaction(function () use ($business, $actor, $reason, $changes, $event, $description): Business {
            $business = Business::query()->lockForUpdate()->findOrFail($business->id);
            $before = $this->snapshot($business);
            $business->update($changes);
            $business->refresh();
            $this->audit->record($actor, $event, $business, "{$description} {$business->name}.", $before, $this->snapshot($business), ['reason' => trim($reason)]);

            return $business;
        });
    }

    /** @return array<string, string> */
    private function snapshot(Business $business): array
    {
        return [
            'verification_status' => $business->verification_status->value,
            'status' => $business->status->value,
        ];
    }
}
