<?php

namespace App\Models;

use App\Enums\PropertyStaffRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'employee_id', 'assignment_role', 'responsibilities', 'is_primary', 'starts_on', 'ends_on', 'assignment_status', 'status', 'created_by', 'updated_by'])]
class PropertyStaffAssignment extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'assignment_role' => PropertyStaffRole::class,
            'is_primary' => 'boolean',
            'starts_on' => 'date',
            'ends_on' => 'date',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
