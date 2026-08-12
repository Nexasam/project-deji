<?php

namespace App\Enums;

enum PropertyAvailabilityDayState: string
{
    case Held = 'held';
    case Booked = 'booked';
    case Blocked = 'blocked';
}
