<?php

namespace App\Data;

final readonly class BookingPrice
{
    public function __construct(
        public string $currency, public int $nights, public int $subtotalMinor,
        public int $discountMinor, public int $serviceFeeMinor, public int $totalMinor,
    ) {}

    public function decimal(int $minor): string
    {
        return number_format($minor / 100, 4, '.', '');
    }
}
