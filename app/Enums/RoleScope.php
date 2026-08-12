<?php

namespace App\Enums;

enum RoleScope: string
{
    case Public = 'public';
    case Business = 'business';
    case Platform = 'platform';
}
