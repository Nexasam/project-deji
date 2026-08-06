<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'system_key', 'permission_id', 'conflicting_permission_id', 'enforcement',
    'description', 'status', 'created_by', 'updated_by',
])]
class PermissionSeparationRule extends Model
{
    use HasUuids;

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function conflictingPermission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'conflicting_permission_id');
    }
}
