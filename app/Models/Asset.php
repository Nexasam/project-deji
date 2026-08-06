<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'supplier_id', 'name', 'category',
    'serial_number', 'purchase_date', 'warranty', 'condition',
    'replacement_value', 'replacement_currency', 'maintenance_schedule',
    'status', 'created_by', 'updated_by',
])]
class Asset extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Purchased assets cannot be deleted; retire or archive them instead.');
        });
    }

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty' => 'array',
            'replacement_value' => 'decimal:4',
            'maintenance_schedule' => 'array',
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

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenanceRecord::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
