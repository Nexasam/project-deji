<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentResult;
use App\Services\Platform\PlatformSettings;

final class PlatformConfiguredPaymentGateway implements PaymentGateway
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function charge(string $reference, int $amountMinor, string $currency): PaymentResult
    {
        $mode = (string) $this->settings->get('payments.gateway_execution_mode', 'simulated');
        $provider = (string) $this->settings->get('payments.active_provider', 'paystack');

        if ($mode !== 'live_ready') {
            return new PaymentResult(true, 'SIM-'.strtoupper($reference), [
                'simulated' => true,
                'provider' => $provider,
                'mode' => $mode,
            ]);
        }

        $enabled = (bool) $this->settings->get("payments.{$provider}_enabled", false);
        $publicKey = trim((string) $this->settings->get("payments.{$provider}_public_key", ''));
        $secretKey = trim((string) $this->settings->get("payments.{$provider}_secret_key", ''));

        if (! $enabled || $publicKey === '' || $secretKey === '') {
            return new PaymentResult(false, 'CONFIG-MISSING-'.strtoupper($reference), [
                'simulated' => false,
                'provider' => $provider,
                'mode' => $mode,
                'configuration_error' => 'Payment provider is disabled or missing public/secret keys.',
            ]);
        }

        return new PaymentResult(true, 'CFG-'.strtoupper($provider).'-'.strtoupper($reference), [
            'simulated' => true,
            'provider' => $provider,
            'mode' => $mode,
            'configured_credentials_available' => true,
            'note' => 'Hosted checkout and webhook verification are configured in the real-payment activation step.',
        ]);
    }
}
