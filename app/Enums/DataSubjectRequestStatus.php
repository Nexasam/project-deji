<?php

namespace App\Enums;

enum DataSubjectRequestStatus: string
{
    case Received = 'received';
    case IdentityVerification = 'identity_verification';
    case InProgress = 'in_progress';
    case Fulfilled = 'fulfilled';
    case PartiallyFulfilled = 'partially_fulfilled';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
