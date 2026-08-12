<?php

namespace App\Enums;

enum BusinessStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
