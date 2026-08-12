<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'identity_status', 'balance_status', 'security_deposit_status', 'access_method', 'access_reference', 'checklist', 'blocking_issues', 'completed_by', 'completed_at', 'status'])]
class BookingCheckIn extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['checklist' => 'array', 'blocking_issues' => 'array', 'completed_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
