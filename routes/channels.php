<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Booking;
use App\Models\BusinessMembership;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('booking.{bookingId}', function ($user, string $bookingId) {
    $booking = Booking::query()->with('property')->find($bookingId);
    if (! $booking) return false;
    if ($booking->guest_user_id === $user->id) return true;

    $membership = BusinessMembership::query()->with(['business', 'employee.propertyAssignments'])
        ->where('business_id', $booking->business_id)->where('user_id', $user->id)->where('status', 'active')->first();
    $roleAssignment = $membership?->roles()->with('role')->where('status', 'active')->whereNull('revoked_at')->whereHas('role', fn ($query) => $query->where('status', 'active'))->first();
    if (! $membership || ! $roleAssignment) return false;

    $context = new ActiveBusinessContext($membership->business, $membership, $roleAssignment);

    return app(BusinessPermissionService::class)->allows($user, $context, 'booking.communicate', $booking->property);
});
