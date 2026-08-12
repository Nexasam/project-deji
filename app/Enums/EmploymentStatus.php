<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case OnLeave = 'on_leave';
    case Suspended = 'suspended';
    case Ended = 'ended';
}
