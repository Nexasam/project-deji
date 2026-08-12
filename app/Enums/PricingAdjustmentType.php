<?php

namespace App\Enums;

enum PricingAdjustmentType: string
{
    case FixedPrice = 'fixed_price';
    case PercentageIncrease = 'percentage_increase';
    case PercentageDecrease = 'percentage_decrease';
    case AmountIncrease = 'amount_increase';
    case AmountDecrease = 'amount_decrease';
}
