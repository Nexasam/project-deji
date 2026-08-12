<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'sync_run_id', 'external_event_id', 'operation', 'validation_status', 'result_status', 'source_type', 'source_id', 'payload_hash', 'payload', 'validation_errors', 'failure_reason', 'processed_at', 'status', 'created_by', 'updated_by'])]
class ExternalCalendarSyncItem extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['payload' => 'array', 'validation_errors' => 'array', 'processed_at' => 'datetime'];
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(ExternalCalendarSyncRun::class, 'sync_run_id');
    }
}
