<?php

namespace App\Enums;

enum PermissionRiskLevel: string
{
    case Normal = 'normal';
    case Sensitive = 'sensitive';
    case Critical = 'critical';
}
