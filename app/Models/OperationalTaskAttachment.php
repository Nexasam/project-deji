<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'operational_task_id', 'attachment_type', 'disk', 'path', 'original_name', 'mime_type', 'size_bytes', 'checksum', 'caption', 'metadata', 'status'])]
class OperationalTaskAttachment extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OperationalTask::class, 'operational_task_id');
    }
}
