<?php

namespace App\Enums;

enum PricingRuleType: string
{
    case DateRange = 'date_range';
    case DayOfWeek = 'day_of_week';
    case Seasonal = 'seasonal';
    case LengthOfStay = 'length_of_stay';
    case AdvanceBooking = 'advance_booking';
    case LastMinute = 'last_minute';
    case ManualOverride = 'manual_override';
}
