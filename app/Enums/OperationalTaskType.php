<?php

namespace App\Enums;

enum OperationalTaskType: string
{
    case Cleaning = 'cleaning';
    case Maintenance = 'maintenance';
    case Inspection = 'inspection';
    case InventoryCheck = 'inventory_check';
    case GuestWelcome = 'guest_welcome';
    case Photography = 'photography';
    case DeepCleaning = 'deep_cleaning';
    case Repair = 'repair';
}
