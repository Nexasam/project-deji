<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyAvailabilityDay;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use App\Services\Operations\BookingOperationsService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateManualBooking
{
    public function __construct(private readonly PropertyAvailabilityService $availability, private readonly BookingOperationsService $operations, private readonly ProductNotificationService $notifications, private readonly BookingLifecycleService $lifecycle) {}

    public function handle(Property $property, User $actor, array $data): Booking
    {
        if ($existing = Booking::query()->where('business_id', $property->business_id)->where('external_reference', $data['idempotency_key'])->first()) {
            return $existing;
        }

        return DB::transaction(function () use ($property, $actor, $data): Booking {
            $property = Property::query()->with('business')->lockForUpdate()->findOrFail($property->id);
            $arrival = CarbonImmutable::parse($data['arrival_date']);
            $departure = CarbonImmutable::parse($data['departure_date']);
            $guests = (int) $data['adult_count'] + (int) ($data['child_count'] ?? 0);
            if ($guests > $property->capacity) {
                throw ValidationException::withMessages(['adult_count' => 'Guest count exceeds this property capacity.']);
            }
            if (! $this->availability->isAvailable($property, $arrival, $departure)) {
                throw ValidationException::withMessages(['arrival_date' => 'These dates conflict with an existing booking or calendar block.']);
            }

            $guest = User::query()->firstOrCreate(['email' => Str::lower($data['guest_email'])], [
                'name' => $data['guest_name'], 'phone_number' => $data['guest_phone'],
                'password' => Str::password(32), 'timezone' => $property->business->timezone,
            ]);
            $guest->forceFill([
                'name' => $guest->name ?: $data['guest_name'],
                'phone_number' => $guest->phone_number ?: $data['guest_phone'],
            ])->save();
            $nights = $arrival->diffInDays($departure);
            $subtotal = round((float) $data['nightly_rate'] * $nights, 4);
            $paid = min($subtotal, (float) ($data['amount_paid'] ?? 0));
            $paymentStatus = $paid <= 0 ? 'unpaid' : ($paid < $subtotal ? 'partially_paid' : 'paid');
            $reference = 'VS-'.strtoupper(Str::random(10));
            $booking = Booking::query()->create([
                'business_id' => $property->business_id, 'property_id' => $property->id,
                'guest_user_id' => $guest->id, 'reference' => $reference,
                'arrival_date' => $arrival, 'departure_date' => $departure,
                'number_of_guests' => $guests, 'adult_count' => $data['adult_count'],
                'child_count' => $data['child_count'] ?? 0, 'source' => $data['source'],
                'status' => 'awaiting_payment', 'payment_status' => $paymentStatus,
                'currency' => $property->pricing_currency, 'subtotal_amount' => $subtotal,
                'discount_amount' => 0, 'total_amount' => $subtotal,
                'special_requests' => $data['notes'] ?? null,
                'external_reference' => $data['idempotency_key'],
                'source_metadata' => ['created_manually' => true], 'created_by' => $actor->id,
            ]);
            BookingGuest::query()->create([
                'business_id' => $property->business_id, 'booking_id' => $booking->id,
                'user_id' => $guest->id, 'guest_type' => 'adult', 'is_primary' => true,
                'full_name' => $data['guest_name'], 'email' => $data['guest_email'],
                'phone_number' => $data['guest_phone'], 'identity_verification_status' => 'unverified',
                'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id,
            ]);
            BookingStatusHistory::query()->create([
                'business_id' => $property->business_id, 'booking_id' => $booking->id,
                'previous_status' => null, 'new_status' => 'awaiting_payment', 'source' => 'owner',
                'reason' => 'Manual '.$data['source'].' booking created', 'occurred_at' => now(), 'status' => 'active',
            ]);
            if ($paid > 0) {
                Payment::query()->create([
                    'business_id' => $property->business_id, 'booking_id' => $booking->id,
                    'reference' => 'PAY-'.$reference, 'purpose' => $paid < $subtotal ? 'deposit' : 'balance',
                    'amount' => $paid, 'currency' => $property->pricing_currency,
                    'method' => $data['payment_method'], 'provider' => null,
                    'status' => 'completed', 'transaction_at' => now(), 'verified_by' => $actor->id,
                    'verified_at' => now(), 'notes' => 'Owner-recorded manual payment', 'created_by' => $actor->id,
                ]);
            }
            $booking = $this->lifecycle->confirm($booking, $actor, 'owner', 'Owner confirmed manual '.$data['source'].' booking');
            for ($date = $arrival; $date->lt($departure); $date = $date->addDay()) {
                PropertyAvailabilityDay::query()->create([
                    'business_id' => $property->business_id, 'property_id' => $property->id,
                    'booking_id' => $booking->id, 'availability_date' => $date,
                    'availability_state' => 'booked', 'source_type' => $data['source'],
                    'source_reference' => $reference, 'active_key' => 'active', 'allocated_at' => now(),
                    'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id,
                ]);
            }
            $this->operations->onBookingConfirmed($booking, $actor);
            $this->notifications->user($guest, $property->business, 'booking_confirmed', 'Your stay is confirmed', "{$property->name} is reserved from {$arrival->format('d M Y')} to {$departure->format('d M Y')}.", ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]);
            $this->notifications->businessOwners($property->business, 'new_booking', 'New manual booking', "{$data['guest_name']} was booked into {$property->name} via ".str($data['source'])->replace('_', ' ')->title().'.', ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);

            return $booking->fresh();
        });
    }
}
