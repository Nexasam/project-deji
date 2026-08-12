<?php

namespace App\Enums;

enum RevenueRecognitionBasis: string
{
    case Nightly = 'nightly';
    case CheckOut = 'check_out';
    case PaymentReceived = 'payment_received';
    case Manual = 'manual';
}
