<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Services\Booking\BookingMessageService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerMessageController extends Controller
{
    public function index(ActiveBusinessContext $context, BusinessPermissionService $permissions): View
    {
        $propertyIds = $permissions->permittedPropertyIds($context);

        $bookings = $context->business->bookings()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->whereHas('interactions', fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false))
            ->with(['property.marketplaceListing', 'guest', 'interactions' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)->latest('occurred_at')])
            ->withMax(['interactions as latest_message_at' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)], 'occurred_at')
            ->orderByDesc('latest_message_at')
            ->paginate(12);

        return view('owner.messages.index', ['business' => $context->business, 'bookings' => $bookings]);
    }

    public function show(ActiveBusinessContext $context, BusinessPermissionService $permissions, string $booking): View
    {
        $record = $this->findBooking($context, $permissions, $booking);

        return view('owner.messages.show', ['business' => $context->business, 'booking' => $record]);
    }

    public function store(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions, string $booking, BookingMessageService $messages): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $record = $this->findBooking($context, $permissions, $booking);

        $messages->ownerMessage($record, $request->user(), $data['content']);

        return redirect()->route('owner.bookings.messages.show', $record)->with('status', 'Message sent to guest.');
    }

    private function findBooking(ActiveBusinessContext $context, BusinessPermissionService $permissions, string $booking)
    {
        $propertyIds = $permissions->permittedPropertyIds($context);

        return $context->business->bookings()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->with(['business', 'property.marketplaceListing', 'guest', 'interactions' => fn ($query) => $query->whereIn('interaction_type', ['message', 'owner_guest_review'])->where('is_internal', false)->orderBy('occurred_at')])
            ->findOrFail($booking);
    }
}
