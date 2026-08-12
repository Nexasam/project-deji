<?php

namespace App\Enums;

enum DocumentAccessLevel: string
{
    case View = 'view';
    case Download = 'download';
    case Edit = 'edit';
    case Manage = 'manage';
}
