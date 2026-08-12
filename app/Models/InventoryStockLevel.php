<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'inventory_item_id', 'inventory_location_id', 'quantity_on_hand', 'quantity_reserved', 'reorder_level', 'reorder_quantity', 'last_counted_at', 'last_counted_by', 'status'])]
class InventoryStockLevel extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['quantity_on_hand' => 'decimal:4', 'quantity_reserved' => 'decimal:4', 'reorder_level' => 'decimal:4', 'reorder_quantity' => 'decimal:4', 'last_counted_at' => 'datetime'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }
}
