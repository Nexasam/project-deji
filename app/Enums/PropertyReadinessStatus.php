<?php

namespace App\Enums;

enum PropertyReadinessStatus: string
{
    case NotReady = 'not_ready';
    case AwaitingCleaning = 'awaiting_cleaning';
    case AwaitingInspection = 'awaiting_inspection';
    case MaintenanceRequired = 'maintenance_required';
    case Ready = 'ready';
}
