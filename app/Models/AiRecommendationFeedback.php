<?php

namespace App\Models;

use App\Enums\AiRecommendationFeedbackType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'ai_recommendation_id', 'user_id', 'feedback_type', 'reason', 'snoozed_until', 'action_taken', 'outcome', 'outcome_score', 'recorded_at', 'status'])]
class AiRecommendationFeedback extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('AI recommendation feedback is immutable.'));
        static::deleting(fn (): never => throw new LogicException('AI recommendation feedback cannot be deleted.'));
    }

    protected function casts(): array
    {
        return ['feedback_type' => AiRecommendationFeedbackType::class, 'snoozed_until' => 'datetime', 'action_taken' => 'array', 'outcome' => 'array', 'outcome_score' => 'decimal:4', 'recorded_at' => 'datetime'];
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(AiRecommendation::class, 'ai_recommendation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
