<?php

namespace App\Enums;

enum DataSubjectRequestType: string
{
    case Access = 'access';
    case Correction = 'correction';
    case Export = 'export';
    case Restriction = 'restriction';
    case Erasure = 'erasure';
}
