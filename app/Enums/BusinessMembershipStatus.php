<?php

namespace App\Enums;

enum BusinessMembershipStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Inactive = 'inactive';
    case Ended = 'ended';
}
