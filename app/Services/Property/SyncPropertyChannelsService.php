<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\PropertyChannelConnection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SyncPropertyChannelsService
{
    /** @param list<string> $providers */
    public function sync(Property $property, User $actor, array $providers): void
    {
        DB::transaction(function () use ($property, $actor, $providers): void {
            $property->channelConnections()->where('status', 'active')->whereNotIn('provider', $providers)->update([
                'status' => 'inactive',
                'updated_by' => $actor->id,
            ]);

            foreach ($providers as $provider) {
                $connection = PropertyChannelConnection::withTrashed()->firstOrNew([
                    'property_id' => $property->id,
                    'provider' => $provider,
                ]);
                $connection->fill([
                    'business_id' => $property->business_id,
                    'external_reference' => null,
                    'connection_status' => 'pending',
                    'metadata' => ['selected_during_setup' => true],
                    'status' => 'active',
                    'created_by' => $connection->created_by ?: $actor->id,
                    'updated_by' => $actor->id,
                ])->save();
                if ($connection->trashed()) {
                    $connection->restore();
                }
            }
        });
    }
}
