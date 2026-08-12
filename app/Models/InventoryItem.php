<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'supplier_id', 'sku', 'name', 'category', 'description', 'unit_of_measure', 'default_reorder_level', 'default_reorder_quantity', 'unit_cost', 'currency', 'is_trackable', 'status'])]
class InventoryItem extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return ['default_reorder_level' => 'decimal:4', 'default_reorder_quantity' => 'decimal:4', 'unit_cost' => 'decimal:4', 'is_trackable' => 'boolean'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(InventoryStockLevel::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
