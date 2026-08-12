<?php

namespace App\Enums;

enum PropertyPublicationStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Unpublished = 'unpublished';
    case Archived = 'archived';
}
