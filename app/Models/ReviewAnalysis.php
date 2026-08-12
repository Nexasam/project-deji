<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'property_id', 'review_id', 'sentiment', 'sentiment_score', 'summary', 'positive_themes', 'complaint_themes', 'keywords', 'follow_up_priority', 'follow_up_recommended', 'model_provider', 'model_name', 'model_version', 'analysis_version', 'analysed_at', 'status', 'created_by', 'updated_by'])]
class ReviewAnalysis extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Review analyses are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'sentiment_score' => 'decimal:4',
            'positive_themes' => 'array',
            'complaint_themes' => 'array',
            'keywords' => 'array',
            'follow_up_recommended' => 'boolean',
            'analysed_at' => 'datetime',
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

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
