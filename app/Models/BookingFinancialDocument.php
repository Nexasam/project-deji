<?php

namespace App\Models;

use App\Enums\FinancialDocumentStatus;
use App\Enums\FinancialDocumentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['business_id', 'booking_id', 'source_document_id', 'payment_id', 'document_number', 'document_type', 'document_status', 'currency', 'subtotal_amount', 'discount_amount', 'tax_amount', 'total_amount', 'paid_amount', 'balance_amount', 'issuer_snapshot', 'recipient_snapshot', 'booking_snapshot', 'notes', 'terms', 'issued_on', 'due_on', 'sent_at', 'paid_at', 'voided_at', 'voided_by', 'void_reason', 'storage_disk', 'storage_path', 'checksum', 'status', 'created_by', 'updated_by'])]
class BookingFinancialDocument extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Booking financial documents cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'document_type' => FinancialDocumentType::class,
            'document_status' => FinancialDocumentStatus::class,
            'subtotal_amount' => 'decimal:4', 'discount_amount' => 'decimal:4',
            'tax_amount' => 'decimal:4', 'total_amount' => 'decimal:4',
            'paid_amount' => 'decimal:4', 'balance_amount' => 'decimal:4',
            'issuer_snapshot' => 'array', 'recipient_snapshot' => 'array', 'booking_snapshot' => 'array',
            'issued_on' => 'date', 'due_on' => 'date', 'sent_at' => 'datetime',
            'paid_at' => 'datetime', 'voided_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_document_id');
    }

    public function derivedDocuments(): HasMany
    {
        return $this->hasMany(self::class, 'source_document_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingFinancialDocumentItem::class, 'financial_document_id')->orderBy('sort_order');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(BookingPaymentInstallment::class, 'financial_document_id');
    }
}
