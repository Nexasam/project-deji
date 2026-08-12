<?php

namespace App\Models;

use App\Enums\InventoryMovementType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['business_id', 'inventory_item_id', 'source_location_id', 'destination_location_id', 'property_id', 'booking_id', 'operational_task_id', 'employee_id', 'supplier_id', 'movement_type', 'quantity', 'unit_cost', 'currency', 'reference', 'reason', 'occurred_at', 'metadata', 'status'])]
class InventoryMovement extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Inventory movements are immutable.'));
        static::deleting(fn (): never => throw new LogicException('Inventory movements cannot be deleted.'));
    }

    protected function casts(): array
    {
        return ['movement_type' => InventoryMovementType::class, 'quantity' => 'decimal:4', 'unit_cost' => 'decimal:4', 'occurred_at' => 'datetime', 'metadata' => 'array'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'destination_location_id');
    }
}
