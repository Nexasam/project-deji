<?php

namespace App\Enums;

enum FinancialTransactionDirection: string
{
    case Inflow = 'inflow';
    case Outflow = 'outflow';
    case NonCash = 'non_cash';
}
