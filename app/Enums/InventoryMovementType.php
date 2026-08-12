<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case Receipt = 'receipt';
    case Consumption = 'consumption';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
    case Damage = 'damage';
    case Return = 'return';
}
