<?php

namespace App\Services\Booking;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PayBookingBalance
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly ProductNotificationService $notifications,
    ) {}

    public function handle(Booking $booking, User $guest, string $provider): Booking
    {
        return DB::transaction(function () use ($booking, $guest, $provider): Booking {
            $booking = Booking::query()
                ->whereKey($booking->id)
                ->where('guest_user_id', $guest->id)
                ->with(['business', 'property', 'payments'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($booking->status->value, ['reserved', 'awaiting_payment', 'confirmed'], true)) {
                throw ValidationException::withMessages(['payment' => 'This booking is not eligible for balance payment.']);
            }

            $paid = (float) $booking->payments
                ->filter(fn (Payment $payment) => $payment->status->value === 'completed' && $payment->purpose->value !== 'refund')
                ->sum('amount');
            $outstanding = round(max(0, (float) $booking->total_amount - $paid), 2);

            if ($outstanding <= 0) {
                throw ValidationException::withMessages(['payment' => 'This booking is already fully paid.']);
            }

            $reference = 'BAL-'.$booking->reference.'-'.Str::upper(Str::random(6));
            $result = $this->gateway->charge($reference, (int) round($outstanding * 100), $booking->currency, ['booking_id' => $booking->id, 'provider' => $provider]);

            Payment::query()->create([
                'business_id' => $booking->business_id,
                'booking_id' => $booking->id,
                'reference' => $reference,
                'purpose' => 'balance',
                'amount' => $outstanding,
                'currency' => $booking->currency,
                'method' => 'card',
                'provider' => $provider,
                'provider_reference' => $result->providerReference,
                'status' => $result->pending ? 'pending' : ($result->successful ? 'completed' : 'failed'),
                'transaction_at' => now(),
                'verified_by' => $guest->id,
                'verified_at' => $result->successful ? now() : null,
                'provider_metadata' => $result->metadata + ['payment_option' => 'balance', 'initiated_by' => $guest->id],
                'created_by' => $guest->id,
            ]);

            if ($result->pending) {
                $booking->setAttribute('payment_authorization_url', $result->authorizationUrl);
                return $booking;
            }

            if (! $result->successful) {
                throw ValidationException::withMessages(['payment' => 'The balance payment could not be completed. Please check the gateway configuration or try again.']);
            }

            $updates = ['payment_status' => 'paid'];
            if ($booking->status->value === 'awaiting_payment') {
                $updates['status'] = 'confirmed';
            }

            $booking->forceFill($updates)->save();

            $this->notifications->user(
                $guest,
                $booking->business,
                'booking.balance_paid',
                'Balance payment received',
                "Your remaining balance for {$booking->reference} has been paid. Your arrival guide is now unlocked.",
                ['booking_id' => $booking->id, 'route' => route('guest.bookings.show', $booking)],
            );

            if ($booking->business) {
                $this->notifications->businessOwners(
                    $booking->business,
                    'booking.balance_paid',
                    'Booking fully paid',
                    "{$booking->reference} is now fully paid and ready for check-in.",
                    ['booking_id' => $booking->id, 'route' => route('owner.bookings.show', $booking)],
                );
            }

            return $booking->refresh()->load(['business', 'property', 'payments']);
        });
    }
}
