<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'step_key', 'sort_order', 'is_required', 'state', 'completed_at', 'skipped_at', 'completed_by', 'metadata', 'status', 'created_by', 'updated_by'])]
class PropertySetupStep extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_required' => 'boolean', 'completed_at' => 'datetime', 'skipped_at' => 'datetime', 'metadata' => 'array'];
    }

    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
}
