<?php

namespace App\Models;

use App\Enums\BookingGuestType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'booking_id', 'user_id', 'guest_type', 'is_primary', 'full_name', 'email', 'phone_number', 'nationality', 'date_of_birth', 'identity_verification_status', 'preferences', 'notes', 'status', 'created_by', 'updated_by'])]
class BookingGuest extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['guest_type' => BookingGuestType::class, 'is_primary' => 'boolean', 'date_of_birth' => 'date', 'preferences' => 'array'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
