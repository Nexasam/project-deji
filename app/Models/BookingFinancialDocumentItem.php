<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'financial_document_id', 'item_type', 'description', 'quantity', 'unit_amount', 'subtotal_amount', 'discount_amount', 'tax_rate', 'tax_amount', 'total_amount', 'metadata', 'sort_order', 'status', 'created_by', 'updated_by'])]
class BookingFinancialDocumentItem extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Issued financial document items cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4', 'unit_amount' => 'decimal:4',
            'subtotal_amount' => 'decimal:4', 'discount_amount' => 'decimal:4',
            'tax_rate' => 'decimal:4', 'tax_amount' => 'decimal:4',
            'total_amount' => 'decimal:4', 'metadata' => 'array', 'sort_order' => 'integer',
        ];
    }

    public function financialDocument(): BelongsTo
    {
        return $this->belongsTo(BookingFinancialDocument::class, 'financial_document_id');
    }
}
