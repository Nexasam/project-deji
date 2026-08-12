<?php

namespace App\Enums;

enum FinancialDocumentStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case Sent = 'sent';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Void = 'void';
    case Cancelled = 'cancelled';
}
