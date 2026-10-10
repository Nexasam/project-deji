<?php

namespace App\Data;

final readonly class PaymentResult
{
    public function __construct(
        public bool $successful,
        public string $providerReference,
        public array $metadata = [],
        public ?string $authorizationUrl = null,
        public bool $pending = false,
    ) {}
}
