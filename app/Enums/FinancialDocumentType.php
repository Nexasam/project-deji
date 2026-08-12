<?php

namespace App\Enums;

enum FinancialDocumentType: string
{
    case Quotation = 'quotation';
    case ProFormaInvoice = 'pro_forma_invoice';
    case TaxInvoice = 'tax_invoice';
    case Receipt = 'receipt';
    case CreditNote = 'credit_note';
    case RefundReceipt = 'refund_receipt';
}
