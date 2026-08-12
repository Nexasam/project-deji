<?php

namespace App\Enums;

enum FinancialTransactionType: string
{
    case GuestPayment = 'guest_payment';
    case Refund = 'refund';
    case Expense = 'expense';
    case Commission = 'commission';
    case SubscriptionFee = 'subscription_fee';
    case OwnerWithdrawal = 'owner_withdrawal';
    case SupplierPayment = 'supplier_payment';
    case DamageCharge = 'damage_charge';
    case SecurityDeposit = 'security_deposit';
    case Tax = 'tax';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
}
