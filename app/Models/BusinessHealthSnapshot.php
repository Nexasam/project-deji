<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'snapshot_date', 'period_type', 'score', 'component_scores', 'explanations', 'supporting_metrics', 'calculation_version', 'calculated_at', 'status', 'created_by', 'updated_by'])]
class BusinessHealthSnapshot extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'score' => 'integer',
            'component_scores' => 'array',
            'explanations' => 'array',
            'supporting_metrics' => 'array',
            'calculated_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
