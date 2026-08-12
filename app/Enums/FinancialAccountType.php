<?php

namespace App\Enums;

enum FinancialAccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Wallet = 'wallet';
    case PaymentProvider = 'payment_provider';
    case Clearing = 'clearing';
}
