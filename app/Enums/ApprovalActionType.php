<?php

namespace App\Enums;

enum ApprovalActionType: string
{
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Returned = 'returned';
    case Escalated = 'escalated';
    case Delegated = 'delegated';
    case Cancelled = 'cancelled';
}
