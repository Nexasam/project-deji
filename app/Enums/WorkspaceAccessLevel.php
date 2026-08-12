<?php

namespace App\Enums;

enum WorkspaceAccessLevel: string
{
    case Limited = 'limited';
    case Full = 'full';
}
