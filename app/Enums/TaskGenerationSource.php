<?php

namespace App\Enums;

enum TaskGenerationSource: string
{
    case Manual = 'manual';
    case Recurring = 'recurring';
    case Ai = 'ai';
    case System = 'system';
}
