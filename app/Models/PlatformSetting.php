<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group_key', 'key', 'value', 'value_type', 'label', 'description', 'updated_by'])]
class PlatformSetting extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
