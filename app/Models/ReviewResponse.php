<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'review_id', 'responded_by', 'content', 'moderation_status', 'submitted_at', 'published_at', 'status'])]
class ReviewResponse extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'published_at' => 'datetime'];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
