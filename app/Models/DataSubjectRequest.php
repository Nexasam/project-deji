<?php

namespace App\Models;

use App\Enums\DataSubjectRequestStatus;
use App\Enums\DataSubjectRequestType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'user_id', 'reference', 'request_type', 'request_status', 'requester_name', 'requester_email', 'identity_verified_at', 'received_at', 'due_at', 'completed_at', 'resolution_summary', 'legal_hold_reason', 'request_data', 'status'])]
class DataSubjectRequest extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['request_type' => DataSubjectRequestType::class, 'request_status' => DataSubjectRequestStatus::class, 'identity_verified_at' => 'datetime', 'received_at' => 'datetime', 'due_at' => 'datetime', 'completed_at' => 'datetime', 'request_data' => 'array'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
