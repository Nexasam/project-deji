<?php

namespace App\Enums;

enum PropertyPromotionType: string
{
    case Seasonal = 'seasonal';
    case EarlyBooking = 'early_booking';
    case LastMinute = 'last_minute';
    case LengthOfStay = 'length_of_stay';
    case NewListing = 'new_listing';
    case ReturningGuest = 'returning_guest';
    case Coupon = 'coupon';
    case Manual = 'manual';
}
