<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentProvider;
use App\Enums\PaymentPurpose;
use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable([
    'business_id',
    'booking_id',
    'original_payment_id',
    'reference',
    'purpose',
    'amount',
    'currency',
    'method',
    'provider',
    'provider_reference',
    'status',
    'transaction_at',
    'verified_by',
    'verified_at',
    'receipt_disk',
    'receipt_path',
    'receipt_url',
    'provider_metadata',
    'notes',
    'created_by',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::saving(function (Payment $payment): void {
            if ((float) $payment->amount <= 0) {
                throw new LogicException('Payment amounts must be greater than zero.');
            }

            $purpose = $payment->purpose instanceof PaymentPurpose
                ? $payment->purpose
                : PaymentPurpose::from($payment->purpose);

            if ($purpose->isRefund() && $payment->original_payment_id === null) {
                throw new LogicException('Refunds must reference their original payment.');
            }

            if (! $purpose->isRefund() && $payment->original_payment_id !== null) {
                throw new LogicException('Only refund transactions may reference an original payment.');
            }

            if ($payment->original_payment_id === null) {
                return;
            }

            $validOriginalPayment = self::query()
                ->whereKey($payment->original_payment_id)
                ->where('business_id', $payment->business_id)
                ->where('booking_id', $payment->booking_id)
                ->where('currency', $payment->currency)
                ->where('purpose', '!=', PaymentPurpose::Refund->value)
                ->exists();

            if (! $validOriginalPayment) {
                throw new LogicException(
                    'The original payment must belong to the same business, booking, and currency.'
                );
            }
        });

        static::deleting(function (): never {
            throw new LogicException('Payments are auditable records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'purpose' => PaymentPurpose::class,
            'amount' => 'decimal:4',
            'method' => PaymentMethod::class,
            'provider' => PaymentProvider::class,
            'status' => PaymentStatus::class,
            'transaction_at' => 'datetime',
            'verified_at' => 'datetime',
            'provider_metadata' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function originalPayment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(self::class, 'original_payment_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function financialAllocations(): HasMany
    {
        return $this->hasMany(BookingFinancialAllocation::class);
    }

    public function financialDocuments(): HasMany
    {
        return $this->hasMany(BookingFinancialDocument::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class, 'original_payment_id');
    }
}
