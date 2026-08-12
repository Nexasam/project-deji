<?php

namespace App\Enums;

enum BusinessOnboardingStatus: string
{
    case Registered = 'registered';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
