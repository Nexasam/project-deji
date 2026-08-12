<?php

namespace App\Enums;

enum PaymentPurpose: string
{
    case Deposit = 'deposit';
    case Balance = 'balance';
    case DamageDeposit = 'damage_deposit';
    case Refund = 'refund';
    case AdditionalCharge = 'additional_charge';
    case LateFee = 'late_fee';
    case CleaningCharge = 'cleaning_charge';

    public function isRefund(): bool
    {
        return $this === self::Refund;
    }
}
