<?php

namespace App\Data;

final readonly class VerifiedPayment
{
    public function __construct(
        public string $provider,
        public string $reference,
        public string $providerTransactionId,
        public int $amountMinor,
        public string $currency,
        public array $metadata = [],
    ) {}
}
