<?php

namespace App\Enums;

enum BookingDisputeStatus: string
{
    case Open = 'open';
    case UnderReview = 'under_review';
    case AwaitingGuest = 'awaiting_guest';
    case AwaitingBusiness = 'awaiting_business';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
    case Closed = 'closed';
}
