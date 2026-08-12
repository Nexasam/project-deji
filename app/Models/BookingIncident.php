<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'property_id', 'booking_id', 'reported_by', 'operational_task_id', 'document_id', 'incident_type', 'severity', 'description', 'financial_impact', 'currency', 'resolution', 'occurred_at', 'resolved_at', 'status'])]
class BookingIncident extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['financial_impact' => 'decimal:4', 'occurred_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(BookingDispute::class);
    }
}
