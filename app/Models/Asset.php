<?php

namespace App\Models;

use App\Enums\AssetCondition;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'supplier_id', 'asset_code', 'name', 'category',
    'serial_number', 'qr_identifier', 'qr_token_hash', 'qr_token_rotated_at',
    'purchase_date', 'warranty_expires_on', 'condition',
    'replacement_value', 'currency', 'notes', 'status',
])]
class Asset extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Purchased assets are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expires_on' => 'date',
            'qr_token_rotated_at' => 'datetime',
            'condition' => AssetCondition::class,
            'replacement_value' => 'decimal:4',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(AssetMaintenanceSchedule::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenanceRecord::class);
    }

    public function maintenanceIssues(): HasMany
    {
        return $this->hasMany(MaintenanceIssue::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(AssetMedia::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
