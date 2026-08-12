<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'title', 'access_method', 'instructions', 'secret_payload', 'effective_at', 'expires_at', 'version', 'status', 'created_by', 'updated_by'])]
class PropertyAccessInstruction extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['secret_payload' => 'encrypted', 'effective_at' => 'datetime', 'expires_at' => 'datetime', 'version' => 'integer'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
