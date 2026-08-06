<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['key', 'name', 'description', 'sort_order', 'status', 'created_by', 'updated_by'])]
class Workspace extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_workspaces')
            ->withPivot(['access_level', 'granted_by'])
            ->withTimestamps();
    }
}
