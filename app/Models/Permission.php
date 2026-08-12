<?php

namespace App\Models;

use App\Enums\PermissionRiskLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'workspace_id', 'key', 'category', 'action', 'description', 'risk_level',
    'requires_audit', 'is_sensitive', 'status',
])]
class Permission extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'risk_level' => PermissionRiskLevel::class,
            'requires_audit' => 'boolean',
            'is_sensitive' => 'boolean',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot(['id', 'status'])
            ->withTimestamps();
    }
}
