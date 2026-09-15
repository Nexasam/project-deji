<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\GuestServiceRequest;
use App\Models\OperationalTask;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ManageGuestServiceRequest
{
    public function __construct(private readonly ProductNotificationService $notifications) {}

    public function create(Booking $booking, User $guest, array $data): GuestServiceRequest
    {
        return DB::transaction(function () use ($booking, $guest, $data): GuestServiceRequest {
            $booking = Booking::query()->with(['business', 'property'])->where('guest_user_id', $guest->id)
                ->lockForUpdate()->findOrFail($booking->id);
            if ($booking->status->value !== 'checked_in') {
                throw ValidationException::withMessages(['booking' => 'Service requests are available after check-in.']);
            }

            $request = GuestServiceRequest::query()->create([
                'business_id' => $booking->business_id,
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'guest_user_id' => $guest->id,
                'request_type' => $data['request_type'],
                'priority' => $data['priority'],
                'description' => $data['description'],
                'requested_at' => now(),
                'status' => 'open',
            ]);

            if (in_array($data['request_type'], ['cleaning', 'maintenance'], true)) {
                $task = OperationalTask::query()->create([
                    'business_id' => $booking->business_id,
                    'property_id' => $booking->property_id,
                    'booking_id' => $booking->id,
                    'reference' => 'GSR-'.strtoupper(Str::random(10)),
                    'title' => str($data['request_type'])->title().' request from guest',
                    'task_type' => $data['request_type'],
                    'priority' => $data['priority'],
                    'status' => 'pending',
                    'due_at' => $data['priority'] === 'urgent' ? now()->addHour() : now()->addHours(4),
                    'notes' => $data['description'],
                    'generation_source' => 'system',
                    'generation_metadata' => ['workflow_stage' => 'guest_service_request', 'service_request_id' => $request->id],
                    'created_by' => $guest->id,
                    'updated_by' => $guest->id,
                ]);
                $request->update(['operational_task_id' => $task->id]);
            }

            $url = route('owner.bookings.show', $booking);
            $this->notifications->businessOwners($booking->business, 'guest_service_request', 'Guest needs assistance',
                "{$guest->name} sent a {$data['priority']} {$data['request_type']} request for {$booking->property->name}.", ['url' => $url, 'booking_id' => $booking->id]);
            $this->notifications->user($guest, $booking->business, 'guest_service_request_received', 'Request received',
                'Your host has received your request and can now acknowledge or resolve it.', ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);

            return $request->fresh('task');
        });
    }

    public function update(GuestServiceRequest $request, User $actor, array $data): GuestServiceRequest
    {
        return DB::transaction(function () use ($request, $data): GuestServiceRequest {
            $request = GuestServiceRequest::query()->with(['booking.business', 'guest'])->lockForUpdate()->findOrFail($request->id);
            if ($data['action'] === 'acknowledge') {
                if ($request->status !== 'open') {
                    throw ValidationException::withMessages(['action' => 'Only an open request can be acknowledged.']);
                }
                $request->update(['status' => 'acknowledged']);
                $type = 'guest_service_request_acknowledged';
                $title = 'Your request was acknowledged';
                $message = 'Your host is now working on your request.';
            } else {
                if (! in_array($request->status, ['open', 'acknowledged'], true)) {
                    throw ValidationException::withMessages(['action' => 'This request has already been resolved.']);
                }
                $request->update(['status' => 'resolved', 'resolution' => $data['resolution'], 'resolved_at' => now()]);
                $type = 'guest_service_request_resolved';
                $title = 'Your request was resolved';
                $message = $data['resolution'];
            }

            $this->notifications->user($request->guest, $request->booking->business, $type, $title, $message, [
                'url' => route('guest.bookings.show', $request->booking),
                'booking_id' => $request->booking_id,
            ]);

            return $request->fresh();
        });
    }
}
