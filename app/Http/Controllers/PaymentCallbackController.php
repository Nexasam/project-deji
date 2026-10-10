<?php

namespace App\Http\Controllers;

use App\Services\Payments\CompleteVerifiedPayment;
use App\Services\Payments\ProviderPaymentVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentCallbackController extends Controller
{
    public function paystack(Request $request, ProviderPaymentVerifier $verifier, CompleteVerifiedPayment $complete): RedirectResponse
    {
        $reference = (string) $request->query('reference');
        abort_if($reference === '', 400, 'Missing payment reference.');

        try {
            $payment = $complete->handle($verifier->paystack($reference));
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('guest.bookings.index')->withErrors(['payment' => 'We could not confirm this payment yet. Please check your booking again shortly.']);
        }

        return redirect()->route('guest.bookings.confirmation', $payment->booking_id)->with('status', 'Payment confirmed successfully.');
    }

    public function flutterwave(Request $request, ProviderPaymentVerifier $verifier, CompleteVerifiedPayment $complete): RedirectResponse
    {
        $validated = $request->validate(['transaction_id' => ['required'], 'tx_ref' => ['required', 'string'], 'status' => ['required', 'string']]);
        if ($validated['status'] !== 'successful') {
            return redirect()->route('guest.bookings.index')->withErrors(['payment' => 'The payment was not completed. You can retry from your booking.']);
        }

        try {
            $verified = $verifier->flutterwave($validated['transaction_id']);
            abort_unless(hash_equals($validated['tx_ref'], $verified->reference), 422, 'Payment reference mismatch.');
            $payment = $complete->handle($verified);
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('guest.bookings.index')->withErrors(['payment' => 'We could not confirm this payment yet. Please check your booking again shortly.']);
        }

        return redirect()->route('guest.bookings.confirmation', $payment->booking_id)->with('status', 'Payment confirmed successfully.');
    }
}
