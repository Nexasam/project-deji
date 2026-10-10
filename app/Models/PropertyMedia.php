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

    public function youtubeEmbedUrl(): ?string
    {
        if ($this->media_type !== PropertyMediaType::Video || blank($this->external_url)) return null;
        $url = (string) $this->external_url;
        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $matches) !== 1) return null;

        return 'https://www.youtube-nocookie.com/embed/'.$matches[1].'?rel=0';
    }
}
