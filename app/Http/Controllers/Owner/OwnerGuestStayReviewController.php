<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookingInteraction;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerGuestStayReviewController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sentiment' => ['required', 'in:good,neutral,bad'],
            'content' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $propertyIds = $permissions->permittedPropertyIds($context);
        $record = $context->business->bookings()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->with(['guest'])
            ->findOrFail($booking);

        BookingInteraction::query()->create([
            'business_id' => $record->business_id,
            'booking_id' => $record->id,
            'user_id' => $request->user()->id,
            'recipient_user_id' => $record->guest_user_id,
            'interaction_type' => 'owner_guest_review',
            'direction' => 'internal',
            'channel' => 'platform',
            'recipient_name' => $record->guest?->name,
            'recipient_address' => $record->guest?->email,
            'summary' => Str::limit($data['content'], 120),
            'content' => $data['content'],
            'is_internal' => false,
            'delivery_status' => 'recorded',
            'occurred_at' => now(),
            'metadata' => ['rating' => (int) $data['rating'], 'sentiment' => $data['sentiment']],
            'status' => 'active',
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('owner.bookings.show', $record)->with('status', 'Guest stay review recorded.');
    }
}
