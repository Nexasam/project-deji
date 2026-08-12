<?php

namespace App\Models;

use App\Enums\PropertyMediaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'property_id',
    'media_type',
    'storage_disk',
    'storage_path',
    'external_url',
    'title',
    'alt_text',
    'sort_order',
    'is_primary',
    'status',
])]
class PropertyMedia extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'media_type' => PropertyMediaType::class,
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
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
