<?php

namespace App\Services\Property;

use App\Models\Asset;
use App\Models\Property;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SyncPropertyAssetsService
{
    /** @param Collection<int, string> $names */
    public function sync(Property $property, Collection $names): void
    {
        DB::transaction(function () use ($property, $names): void {
            $property->assets()->where('status', 'active')->whereNotIn('name', $names->all())->update(['status' => 'inactive']);

            foreach ($names as $name) {
                $asset = Asset::query()->firstOrNew(['property_id' => $property->id, 'name' => $name]);
                $asset->fill([
                    'business_id' => $property->business_id,
                    'asset_code' => $asset->asset_code ?: 'AST-'.Str::upper(Str::random(10)),
                    'qr_identifier' => $asset->qr_identifier ?: (string) Str::uuid(),
                    'category' => 'furnishing',
                    'condition' => 'good',
                    'currency' => $property->pricing_currency,
                    'status' => 'active',
                ])->save();
            }
        });
    }
}
