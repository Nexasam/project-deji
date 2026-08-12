<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'business_membership_id',
    'department_id',
    'employee_code',
    'employment_status',
    'availability',
    'emergency_contact',
    'started_on',
    'ended_on',
    'notes',
    'status',
])]
class Employee extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'employment_status' => EmploymentStatus::class,
            'availability' => 'array',
            'emergency_contact' => 'array',
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function businessMembership(): BelongsTo
    {
        return $this->belongsTo(BusinessMembership::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class, 'assigned_employee_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'inspector_employee_id');
    }

    public function maintenanceIssues(): HasMany
    {
        return $this->hasMany(MaintenanceIssue::class, 'assigned_employee_id');
    }

    public function propertyAssignments(): HasMany
    {
        return $this->hasMany(PropertyStaffAssignment::class);
    }

    public function bookingAssignments(): HasMany
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(OperationalTaskAssignment::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(EmployeeCertification::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
