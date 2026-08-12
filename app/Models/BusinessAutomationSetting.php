<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'scope_key', 'automation_key', 'trigger_event', 'workflow_key', 'is_enabled', 'configuration', 'workflow_version', 'status', 'created_by', 'updated_by'])]
class BusinessAutomationSetting extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'configuration' => 'array', 'workflow_version' => 'integer'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
