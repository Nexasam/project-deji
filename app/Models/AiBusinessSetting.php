<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'ai_enabled', 'enabled_capabilities', 'data_use_preferences', 'automation_limits', 'approval_thresholds', 'permitted_autonomous_actions', 'conversation_retention_days', 'status'])]
class AiBusinessSetting extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['ai_enabled' => 'boolean', 'enabled_capabilities' => 'array', 'data_use_preferences' => 'array', 'automation_limits' => 'array', 'approval_thresholds' => 'array', 'permitted_autonomous_actions' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
