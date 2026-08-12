<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'provider', 'external_calendar_id', 'sync_direction', 'feed_url', 'credentials', 'sync_cursor', 'sync_status', 'last_synced_at', 'last_imported_at', 'last_exported_at', 'consecutive_failure_count', 'last_error', 'status'])]
class ExternalCalendarConnection extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'last_synced_at' => 'datetime',
            'last_imported_at' => 'datetime',
            'last_exported_at' => 'datetime',
            'consecutive_failure_count' => 'integer',
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

    public function syncRuns(): HasMany
    {
        return $this->hasMany(ExternalCalendarSyncRun::class, 'external_calendar_connection_id');
    }
}
