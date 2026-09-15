<?php

namespace App\Services\Notifications;

use App\Jobs\SendNotificationEmail;
use App\Models\Business;
use App\Models\Notification;
use App\Models\User;

final class ProductNotificationService
{
    public function user(User $user, ?Business $business, string $type, string $title, string $message, array $data = []): Notification
    {
        $notification = Notification::query()->create([
            'business_id' => $business?->id, 'user_id' => $user->id, 'type' => $type,
            'title' => $title, 'message' => $message, 'data' => $data,
            'read_at' => null, 'status' => 'active',
        ]);
        if (filled($user->email)) {
            $delivery = $notification->deliveries()->create([
                'business_id' => $business?->id,
                'channel' => 'email',
                'destination' => $user->email,
                'status' => 'pending',
                'attempt_count' => 0,
            ]);
            SendNotificationEmail::dispatch($delivery->id)->afterCommit();
        }

        return $notification;
    }

    public function businessOwners(Business $business, string $type, string $title, string $message, array $data = []): void
    {
        $ownerIds = $business->memberships()->where('status', 'active')
            ->whereHas('roles.role', fn ($query) => $query->where('system_key', 'business_owner')->where('status', 'active'))
            ->pluck('user_id')->unique();
        User::query()->whereIn('id', $ownerIds)->each(fn (User $owner) => $this->user($owner, $business, $type, $title, $message, $data));
    }

    public function platformAdmins(?Business $business, string $type, string $title, string $message, array $data = []): void
    {
        User::query()->whereHas('roleAssignments', fn ($query) => $query
            ->whereNull('business_membership_id')->where('status', 'active')->whereNull('revoked_at')
            ->whereHas('role', fn ($role) => $role->where('system_key', 'platform_super_admin')->where('status', 'active')))
            ->each(fn (User $admin) => $this->user($admin, $business, $type, $title, $message, $data));
    }
}
