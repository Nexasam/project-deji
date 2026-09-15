<?php

namespace App\Models;

use App\Enums\BookingDisputeStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LogicException;

#[Fillable(['business_id', 'property_id', 'booking_id', 'booking_incident_id', 'financial_document_id', 'payment_id', 'reference', 'dispute_type', 'opened_by_type', 'opened_by', 'description', 'priority', 'disputed_amount', 'currency', 'dispute_status', 'assigned_to', 'due_at', 'resolution', 'approved_amount', 'resolved_by', 'opened_at', 'resolved_at', 'closed_at', 'status', 'created_by', 'updated_by'])]
class BookingDispute extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Booking disputes cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['disputed_amount' => 'decimal:4', 'approved_amount' => 'decimal:4', 'dispute_status' => BookingDisputeStatus::class, 'due_at' => 'datetime', 'opened_at' => 'datetime', 'resolved_at' => 'datetime', 'closed_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(BookingIncident::class, 'booking_incident_id');
    }

    public function financialDocument(): BelongsTo
    {
        return $this->belongsTo(BookingFinancialDocument::class, 'financial_document_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
