<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id', 'asset_id', 'name', 'frequency', 'interval', 'starts_on',
    'next_due_on', 'instructions', 'status',
])]
class AssetMaintenanceSchedule extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['interval' => 'integer', 'starts_on' => 'date', 'next_due_on' => 'date'];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
