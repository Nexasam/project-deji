<?php

namespace App\Services\Payments;

use App\Data\VerifiedPayment;
use App\Services\Platform\PlatformSettings;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class ProviderPaymentVerifier
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function paystack(string $reference): VerifiedPayment
    {
        $response = Http::acceptJson()->withToken($this->secret('paystack'))->timeout(20)->retry(2, 250)
            ->get('https://api.paystack.co/transaction/verify/'.rawurlencode($reference))->throw()->json();
        $data = data_get($response, 'data', []);
        if (! data_get($response, 'status') || data_get($data, 'status') !== 'success') {
            throw new RuntimeException('Paystack has not confirmed this transaction as successful.');
        }

        return new VerifiedPayment('paystack', (string) data_get($data, 'reference'), (string) data_get($data, 'id'), (int) data_get($data, 'amount'), strtoupper((string) data_get($data, 'currency')), $data);
    }

    public function flutterwave(string|int $transactionId): VerifiedPayment
    {
        $response = Http::acceptJson()->withToken($this->secret('flutterwave'))->timeout(20)->retry(2, 250)
            ->get('https://api.flutterwave.com/v3/transactions/'.rawurlencode((string) $transactionId).'/verify')->throw()->json();
        $data = data_get($response, 'data', []);
        if (data_get($response, 'status') !== 'success' || data_get($data, 'status') !== 'successful') {
            throw new RuntimeException('Flutterwave has not confirmed this transaction as successful.');
        }

        return new VerifiedPayment('flutterwave', (string) data_get($data, 'tx_ref'), (string) data_get($data, 'id'), (int) round(((float) data_get($data, 'amount')) * 100), strtoupper((string) data_get($data, 'currency')), $data);
    }

    private function secret(string $provider): string
    {
        $secret = $this->settings->paymentCredential($provider, 'secret_key');
        if ($secret === '') {
            throw new RuntimeException(ucfirst($provider).' secret key is not configured.');
        }

        return $secret;
    }
}
