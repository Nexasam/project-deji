<?php

namespace App\Enums;

enum PropertyStaffRole: string
{
    case PropertyManager = 'property_manager';
    case AssistantManager = 'assistant_manager';
    case Cleaner = 'cleaner';
    case Inspector = 'inspector';
    case MaintenanceTechnician = 'maintenance_technician';
    case GuestSupport = 'guest_support';
    case Accountant = 'accountant';
    case Other = 'other';
}
