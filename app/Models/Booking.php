<?php

namespace App\Models;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\DiscountType;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

#[Fillable([
    'business_id',
    'property_id',
    'guest_user_id',
    'reference',
    'arrival_date',
    'departure_date',
    'number_of_guests',
    'adult_count',
    'child_count',
    'pet_count',
    'source',
    'status',
    'payment_status',
    'special_requests',
    'discount_type',
    'discount_value',
    'discount_amount',
    'coupon_code',
    'currency',
    'subtotal_amount',
    'total_amount',
    'external_reference',
    'source_metadata',
    'created_by',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Bookings are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'arrival_date' => 'date',
            'departure_date' => 'date',
            'number_of_guests' => 'integer',
            'adult_count' => 'integer',
            'child_count' => 'integer',
            'pet_count' => 'integer',
            'source' => BookingSource::class,
            'status' => BookingStatus::class,
            'payment_status' => BookingPaymentStatus::class,
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'subtotal_amount' => 'decimal:4',
            'total_amount' => 'decimal:4',
            'source_metadata' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityBlock::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function dateChanges(): HasMany
    {
        return $this->hasMany(BookingDateChange::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(BookingInteraction::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(GuestServiceRequest::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(BookingIncident::class);
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(BookingCheckIn::class);
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(BookingCheckOut::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function financialAllocations(): HasMany
    {
        return $this->hasMany(BookingFinancialAllocation::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(BookingCancellation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookingGuests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function staffAssignments(): HasMany
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    public function financialDocuments(): HasMany
    {
        return $this->hasMany(BookingFinancialDocument::class);
    }

    public function paymentInstallments(): HasMany
    {
        return $this->hasMany(BookingPaymentInstallment::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(BookingDispute::class);
    }

    public function availabilityDays(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityDay::class);
    }

    public function revenueEntries(): HasMany
    {
        return $this->hasMany(RevenueEntry::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }
}
