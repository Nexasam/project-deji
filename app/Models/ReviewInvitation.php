<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'booking_id', 'guest_user_id', 'token_hash', 'sent_at', 'expires_at', 'used_at', 'status'])]
class ReviewInvitation extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'expires_at' => 'datetime', 'used_at' => 'datetime'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
