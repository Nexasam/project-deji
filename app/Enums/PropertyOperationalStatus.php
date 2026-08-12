<?php

namespace App\Enums;

enum PropertyOperationalStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Occupied = 'occupied';
    case Cleaning = 'cleaning';
    case Inspection = 'inspection';
    case Maintenance = 'maintenance';
    case Blocked = 'blocked';
    case Unavailable = 'unavailable';
}
