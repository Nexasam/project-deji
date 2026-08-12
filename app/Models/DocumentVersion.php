<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'document_id', 'version_number', 'storage_disk', 'storage_path',
    'original_name', 'mime_type', 'size_bytes', 'checksum', 'change_summary',
    'uploaded_by', 'status',
])]
class DocumentVersion extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Document versions are immutable.');
        });
        static::deleting(function (): never {
            throw new LogicException('Document versions cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'size_bytes' => 'integer'];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
