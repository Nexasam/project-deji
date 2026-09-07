<?php

namespace App\Contracts\Payments;

use App\Data\PaymentResult;

interface PaymentGateway
{
    public function charge(string $reference, int $amountMinor, string $currency): PaymentResult;
}
