<?php

namespace App\Enums;

enum PropertyMaintenanceStatus: string
{
    case NotRequired = 'not_required';
    case Required = 'required';
    case InProgress = 'in_progress';
    case Unavailable = 'unavailable';
}
