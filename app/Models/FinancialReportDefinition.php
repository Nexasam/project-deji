<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'name', 'report_type', 'configuration', 'is_system', 'status', 'created_by', 'updated_by'])]
class FinancialReportDefinition extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['configuration' => 'array', 'is_system' => 'boolean'];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(FinancialReportSchedule::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(FinancialReportRun::class);
    }
}
