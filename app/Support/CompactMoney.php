<?php

namespace App\Support;

final class CompactMoney
{
    public static function format(float|int $amount, string $currency = 'NGN'): string
    {
        $symbol = $currency === 'NGN' ? '₦' : $currency.' ';
        $negative = $amount < 0;
        $absolute = abs((float) $amount);

        $value = match (true) {
            $absolute >= 1_000_000_000 => self::compact($absolute / 1_000_000_000).'B',
            $absolute >= 1_000_000 => self::compact($absolute / 1_000_000).'M',
            $absolute >= 1_000 => self::compact($absolute / 1_000).'K',
            default => number_format($absolute, $absolute === floor($absolute) ? 0 : 2),
        };

        return ($negative ? '-' : '').$symbol.$value;
    }

    public static function exact(float|int $amount, string $currency = 'NGN'): string
    {
        $symbol = $currency === 'NGN' ? '₦' : $currency.' ';

        return $symbol.number_format((float) $amount, 2);
    }

    private static function compact(float $value): string
    {
        $formatted = $value >= 100 ? number_format($value, 0) : number_format($value, 1);

        return rtrim(rtrim($formatted, '0'), '.');
    }
}
