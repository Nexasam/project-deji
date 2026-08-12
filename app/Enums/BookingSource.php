<?php

namespace App\Enums;

enum BookingSource: string
{
    case Marketplace = 'marketplace';
    case Airbnb = 'airbnb';
    case BookingDotCom = 'booking_dot_com';
    case WhatsApp = 'whatsapp';
    case Referral = 'referral';
    case WalkIn = 'walk_in';
    case Corporate = 'corporate';
    case Phone = 'phone';
    case TravelAgent = 'travel_agent';
    case Manual = 'manual';
}
