<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'user_id', 'default_workspace', 'theme', 'language', 'timezone', 'currency_display', 'kpi_configuration', 'widget_configuration', 'notification_preferences', 'status', 'created_by', 'updated_by'])]
class UserWorkspacePreference extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'kpi_configuration' => 'array',
            'widget_configuration' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
