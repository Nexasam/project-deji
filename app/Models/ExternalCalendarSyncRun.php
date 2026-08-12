<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'external_calendar_connection_id', 'direction', 'trigger_type', 'sync_status', 'started_at', 'completed_at', 'received_count', 'created_count', 'updated_count', 'skipped_count', 'conflict_count', 'failed_count', 'cursor_before', 'cursor_after', 'failure_reason', 'metadata', 'status', 'created_by', 'updated_by'])]
class ExternalCalendarSyncRun extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime', 'completed_at' => 'datetime',
            'received_count' => 'integer', 'created_count' => 'integer',
            'updated_count' => 'integer', 'skipped_count' => 'integer',
            'conflict_count' => 'integer', 'failed_count' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(ExternalCalendarConnection::class, 'external_calendar_connection_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExternalCalendarSyncItem::class, 'sync_run_id');
    }
}
