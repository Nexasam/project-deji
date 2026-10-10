<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentResult;

final class SimulatedPaymentGateway implements PaymentGateway
{
    public function charge(string $reference, int $amountMinor, string $currency, array $context = []): PaymentResult
    {
        return new PaymentResult(true, 'SIM-'.strtoupper($reference), ['simulated' => true]);
    }
}
