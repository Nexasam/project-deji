<?php

namespace App\Enums;

enum UserRoleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Revoked = 'revoked';
}
