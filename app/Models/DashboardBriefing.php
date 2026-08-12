<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'briefing_date', 'title', 'content', 'summary_data', 'supporting_metrics', 'generation_status', 'model_provider', 'model_name', 'model_version', 'prompt_version', 'generation_duration_ms', 'generated_at', 'failure_reason', 'status', 'created_by', 'updated_by'])]
class DashboardBriefing extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'briefing_date' => 'date',
            'summary_data' => 'array',
            'supporting_metrics' => 'array',
            'generation_duration_ms' => 'integer',
            'generated_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
