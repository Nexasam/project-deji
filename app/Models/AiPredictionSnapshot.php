<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['business_id', 'property_id', 'booking_id', 'prediction_type', 'subject_type', 'subject_id', 'predicted_value', 'predicted_label', 'confidence_score', 'factors', 'evidence', 'prediction_for', 'generated_at', 'valid_until', 'model_provider', 'model_name', 'model_version', 'status'])]
class AiPredictionSnapshot extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['predicted_value' => 'decimal:6', 'confidence_score' => 'decimal:4', 'factors' => 'array', 'evidence' => 'array', 'prediction_for' => 'datetime', 'generated_at' => 'datetime', 'valid_until' => 'datetime'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
