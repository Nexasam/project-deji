<?php

namespace App\Enums;

enum AiRecommendationFeedbackType: string
{
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Dismissed = 'dismissed';
    case Snoozed = 'snoozed';
    case PartiallyApplied = 'partially_applied';
    case OutcomeRecorded = 'outcome_recorded';
}
