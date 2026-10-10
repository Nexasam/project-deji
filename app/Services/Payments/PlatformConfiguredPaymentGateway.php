<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentResult;
use App\Models\Booking;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class PlatformConfiguredPaymentGateway implements PaymentGateway
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function charge(string $reference, int $amountMinor, string $currency, array $context = []): PaymentResult
    {
        $booking = Booking::query()->with('guest')->findOrFail($context['booking_id'] ?? null);
        $provider = (string) ($context['provider'] ?? data_get($booking->source_metadata, 'payment_provider', $this->settings->get('payments.active_provider', 'paystack')));
        $enabled = (bool) $this->settings->get("payments.{$provider}_enabled", false);
        $secretKey = $this->settings->paymentCredential($provider, 'secret_key');
        $mode = $this->settings->get('payments.gateway_execution_mode', 'test') === 'live' ? 'live' : 'test';

        if ($enabled && $mode === 'test' && $secretKey === '' && (bool) $this->settings->get('payments.internal_test_checkout_enabled', true)) {
            return new PaymentResult(true, 'TEST-'.strtoupper($reference), [
                'environment' => 'test',
                'provider' => $provider,
                'checkout_path' => 'internal_test',
            ]);
        }

        if (! $enabled || $secretKey === '') {
            throw new RuntimeException(ucfirst($provider).' is not configured for checkout.');
        }

        return match ($provider) {
            'paystack' => $this->initializePaystack($reference, $amountMinor, $currency, $booking->guest->email, $secretKey),
            'flutterwave' => $this->initializeFlutterwave($reference, $amountMinor, $currency, $booking, $secretKey),
            default => throw new RuntimeException('Unsupported payment provider.'),
        };
    }

    private function initializePaystack(string $reference, int $amountMinor, string $currency, string $email, string $secretKey): PaymentResult
    {
        $response = $this->client($secretKey)->post('https://api.paystack.co/transaction/initialize', [
            'email' => $email,
            'amount' => $amountMinor,
            'currency' => strtoupper($currency),
            'reference' => $reference,
            'callback_url' => route('payments.paystack.callback'),
            'metadata' => ['booking_reference' => $reference],
        ])->throw()->json();

        if (! data_get($response, 'status') || blank(data_get($response, 'data.authorization_url'))) {
            throw new RuntimeException('Paystack could not start checkout.');
        }

        $authorizationUrl = (string) data_get($response, 'data.authorization_url');

        return new PaymentResult(false, (string) data_get($response, 'data.reference', $reference), [
            'provider' => 'paystack',
            'environment' => $this->settings->get('payments.gateway_execution_mode', 'test'),
            'access_code' => data_get($response, 'data.access_code'),
            'authorization_url' => $authorizationUrl,
        ], $authorizationUrl, true);
    }

    private function initializeFlutterwave(string $reference, int $amountMinor, string $currency, Booking $booking, string $secretKey): PaymentResult
    {
        $amount = number_format($amountMinor / 100, 2, '.', '');
        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'redirect_url' => route('payments.flutterwave.callback'),
            'customer' => [
                'email' => $booking->guest->email,
                'name' => $booking->guest->name,
                'phonenumber' => $booking->guest->phone_number,
            ],
            'customizations' => [
                'title' => 'Verified Shortlet',
                'description' => 'Payment for booking '.$booking->reference,
            ],
            'meta' => ['booking_reference' => $reference],
        ];
        $payload['payload_hash'] = hash('sha256', $amount.strtoupper($currency).$booking->guest->email.$reference.hash('sha256', $secretKey));

        $response = $this->client($secretKey)->post('https://api.flutterwave.com/v3/payments', $payload)->throw()->json();
        if (data_get($response, 'status') !== 'success' || blank(data_get($response, 'data.link'))) {
            throw new RuntimeException('Flutterwave could not start checkout.');
        }

        $authorizationUrl = (string) data_get($response, 'data.link');

        return new PaymentResult(false, $reference, [
            'provider' => 'flutterwave',
            'environment' => $this->settings->get('payments.gateway_execution_mode', 'test'),
            'authorization_url' => $authorizationUrl,
        ], $authorizationUrl, true);
    }

    private function client(string $secretKey): PendingRequest
    {
        return Http::acceptJson()->asJson()->withToken($secretKey)->timeout(20)->retry(2, 250);
    }
}
