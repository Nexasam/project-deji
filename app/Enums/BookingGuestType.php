<?php

namespace App\Enums;

enum BookingGuestType: string
{
    case Adult = 'adult';
    case Child = 'child';
    case Infant = 'infant';
}
