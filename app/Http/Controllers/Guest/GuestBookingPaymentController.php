<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\PayBookingBalance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class GuestBookingPaymentController extends Controller
{
    public function payBalance(Request $request, string $booking, PayBookingBalance $service, ProductNotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'payment_provider' => ['required', 'in:paystack,flutterwave'],
        ]);

        $booking = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->findOrFail($booking);

        try {
            $booking = $service->handle($booking, $request->user(), $validated['payment_provider']);
        } catch (Throwable $exception) {
            if ($exception instanceof ValidationException) throw $exception;
            Log::error('Booking balance payment initialization failed.', ['booking_id' => $booking->id, 'provider' => $validated['payment_provider'], 'exception' => $exception]);
            try {
                $notifications->platformAdmins($booking->business, 'platform.provider_attention', 'Payment provider needs attention', ucfirst($validated['payment_provider']).' balance payment could not be started. Review provider status and credentials in Platform configuration.', ['url' => route('admin.settings.index'), 'provider' => $validated['payment_provider'], 'booking_id' => $booking->id]);
            } catch (Throwable $notificationException) {
                Log::error('Could not notify platform administrators about provider failure.', ['exception' => $notificationException]);
            }

            return back()->withErrors(['payment' => 'Payment could not be started right now. The platform operations team has been notified. Please try again shortly.']);
        }

        if (filled($booking->getAttribute('payment_authorization_url'))) {
            return redirect()->away($booking->getAttribute('payment_authorization_url'));
        }

        return redirect()
            ->route('guest.bookings.show', $booking)
            ->with('status', 'Balance payment received. Your booking is now fully paid.');
    }
}
