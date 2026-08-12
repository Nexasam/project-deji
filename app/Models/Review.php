<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

#[Fillable(['business_id', 'property_id', 'booking_id', 'guest_user_id', 'rating', 'cleanliness_rating', 'communication_rating', 'location_rating', 'value_rating', 'accuracy_rating', 'title', 'content', 'is_verified_stay', 'moderation_status', 'sentiment', 'sentiment_score', 'submitted_at', 'published_at', 'status'])]
class Review extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Reviews are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['rating' => 'integer', 'is_verified_stay' => 'boolean', 'sentiment_score' => 'decimal:4', 'submitted_at' => 'datetime', 'published_at' => 'datetime'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(ReviewAnalysis::class);
    }
}
