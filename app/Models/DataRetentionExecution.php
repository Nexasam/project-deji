<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['data_retention_policy_id', 'execution_status', 'cutoff_at', 'started_at', 'completed_at', 'records_examined', 'records_affected', 'records_held', 'errors', 'executed_by', 'status'])]
class DataRetentionExecution extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['cutoff_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'errors' => 'array'];
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(DataRetentionPolicy::class, 'data_retention_policy_id');
    }
}
