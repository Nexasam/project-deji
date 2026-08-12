<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'employee_id', 'document_id', 'name', 'issuing_organization', 'certificate_number', 'issued_on', 'expires_on', 'verification_status', 'verified_at', 'verified_by', 'status'])]
class EmployeeCertification extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['issued_on' => 'date', 'expires_on' => 'date', 'verified_at' => 'datetime'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
