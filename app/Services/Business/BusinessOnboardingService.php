<?php

namespace App\Services\Business;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\BusinessOnboardingStep;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class BusinessOnboardingService
{
    public function onboard(User $user, array $attributes): Business
    {
        return DB::transaction(function () use ($user, $attributes): Business {
            $ownerRole = Role::query()->where('system_key', 'business_owner')->where('status', 'active')->first();
            if (! $ownerRole) {
                throw new RuntimeException('The business owner role has not been configured.');
            }

            $business = Business::query()->create(array_merge($attributes, [
                'primary_contact_name' => $attributes['primary_contact_name'] ?? $user->name,
                'email' => $attributes['email'] ?? $user->email,
                'phone_number' => $attributes['phone_number'] ?? $user->phone_number,
                'onboarding_status' => 'in_progress', 'onboarding_started_at' => now(),
                'verification_status' => 'unverified', 'status' => 'active',
            ]));
            $membership = BusinessMembership::query()->create([
                'business_id' => $business->id, 'user_id' => $user->id,
                'job_title' => 'Owner', 'status' => 'active', 'joined_at' => now(),
            ]);
            $assignment = UserRole::query()->create([
                'user_id' => $user->id, 'role_id' => $ownerRole->id,
                'business_membership_id' => $membership->id, 'status' => 'active',
                'assigned_by' => $user->id, 'assigned_at' => now(),
            ]);

            foreach ([['business_profile', 10, 'completed'], ['add_property', 20, 'pending'], ['verification', 30, 'pending']] as [$key, $order, $state]) {
                BusinessOnboardingStep::query()->create([
                    'business_id' => $business->id, 'step_key' => $key, 'sort_order' => $order,
                    'state' => $state, 'completed_at' => $state === 'completed' ? now() : null,
                    'completed_by' => $state === 'completed' ? $user->id : null, 'status' => 'active',
                ]);
            }

            UserBusinessContext::query()->updateOrCreate(['user_id' => $user->id], [
                'business_id' => $business->id, 'business_membership_id' => $membership->id,
                'active_user_role_id' => $assignment->id, 'switched_at' => now(),
                'status' => 'active', 'created_by' => $user->id, 'updated_by' => $user->id,
            ]);

            return $business;
        });
    }
}
