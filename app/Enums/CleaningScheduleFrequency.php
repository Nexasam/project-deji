<?php

namespace App\Enums;

enum CleaningScheduleFrequency: string
{
    case BetweenStays = 'between_stays';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Custom = 'custom';
}
