<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'provider', 'external_reference', 'connection_status', 'metadata', 'status', 'created_by', 'updated_by'])]
class PropertyChannelConnection extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
