<?php

namespace App\Http\Controllers;

use App\Services\Payments\CompleteVerifiedPayment;
use App\Services\Payments\ProviderPaymentVerifier;
use App\Services\Payments\ProviderRefundService;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentWebhookController extends Controller
{
    public function paystack(Request $request, PlatformSettings $settings, ProviderPaymentVerifier $verifier, CompleteVerifiedPayment $complete, ProviderRefundService $refunds): Response
    {
        // Paystack signs the raw request body with the same secret API key used
        // for server-side API requests; it does not issue a separate webhook key.
        $secret = $settings->paymentCredential('paystack', 'secret_key');
        $expected = hash_hmac('sha512', $request->getContent(), $secret);
        abort_unless($secret !== '' && hash_equals($expected, (string) $request->header('x-paystack-signature')), 401);

        $event = (string) $request->input('event');
        if ($event === 'charge.success' && filled($request->input('data.reference'))) {
            $complete->handle($verifier->paystack((string) $request->input('data.reference')));
        } elseif (str_starts_with($event, 'refund.')) {
            $refunds->handlePaystackWebhook($event, (array) $request->input('data', []));
        }

        return response('', 200);
    }

    public function flutterwave(Request $request, PlatformSettings $settings, ProviderPaymentVerifier $verifier, CompleteVerifiedPayment $complete): Response
    {
        $secret = $settings->paymentCredential('flutterwave', 'webhook_secret');
        abort_unless($secret !== '' && hash_equals($secret, (string) $request->header('verif-hash')), 401);

        $transactionId = $request->input('data.id');
        if (in_array($request->input('event'), ['charge.completed', 'charge.successful'], true) && filled($transactionId)) {
            $complete->handle($verifier->flutterwave($transactionId));
        }

        return response('', 200);
    }
}
