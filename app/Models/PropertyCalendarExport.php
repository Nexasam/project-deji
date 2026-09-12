<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'plain_token', 'token_hash', 'generated_at', 'last_accessed_at', 'revoked_at', 'active_key', 'status', 'created_by', 'updated_by'])]
class PropertyCalendarExport extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['plain_token' => 'encrypted', 'generated_at' => 'datetime', 'last_accessed_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function business(): BelongsTo { return $this->belongsTo(Business::class); }
}
