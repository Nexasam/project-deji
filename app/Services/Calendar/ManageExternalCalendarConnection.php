<?php

namespace App\Services\Calendar;

use App\Models\ExternalCalendarConnection;
use App\Models\Property;
use App\Models\PropertyCalendarExport;
use App\Models\PropertyChannelConnection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ManageExternalCalendarConnection
{
    public function __construct(private readonly ValidateExternalCalendarUrl $urls) {}

    public function save(Property $property, User $actor, string $provider, string $url): ExternalCalendarConnection
    {
        $url = $this->urls->validate($provider, $url);
        return DB::transaction(function () use ($property, $actor, $provider, $url) {
            $connection = ExternalCalendarConnection::withTrashed()->firstOrNew(['property_id' => $property->id, 'provider' => $provider]);
            $connection->fill([
                'business_id' => $property->business_id, 'external_calendar_id' => $property->id.':'.$provider,
                'sync_direction' => 'bidirectional', 'feed_url' => null, 'credentials' => ['feed_url' => $url],
                'sync_status' => 'pending', 'last_error' => null, 'status' => 'active',
                'created_by' => $connection->created_by ?: $actor->id, 'updated_by' => $actor->id,
            ])->save();
            if ($connection->trashed()) $connection->restore();
            $channel = PropertyChannelConnection::withTrashed()->updateOrCreate(
                ['property_id' => $property->id, 'provider' => $provider],
                ['business_id' => $property->business_id, 'connection_status' => 'pending', 'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id]
            );
            if ($channel->trashed()) $channel->restore();
            $this->ensureExport($property, $actor);
            return $connection->fresh();
        });
    }

    public function ensureExport(Property $property, ?User $actor = null): PropertyCalendarExport
    {
        $existing = PropertyCalendarExport::query()->where('property_id', $property->id)->where('active_key', 'active')->first();
        return $existing ?: $this->createExport($property, $actor);
    }

    public function regenerateExport(Property $property, User $actor): PropertyCalendarExport
    {
        return DB::transaction(function () use ($property, $actor) {
            PropertyCalendarExport::query()->where('property_id', $property->id)->where('active_key', 'active')->update([
                'active_key' => null, 'revoked_at' => now(), 'status' => 'revoked', 'updated_by' => $actor->id,
            ]);
            return $this->createExport($property, $actor);
        });
    }

    public function disable(ExternalCalendarConnection $connection, User $actor): void
    {
        DB::transaction(function () use ($connection, $actor) {
            $connection->update(['status' => 'inactive', 'sync_status' => 'disabled', 'updated_by' => $actor->id]);
            $connection->availabilityBlocks()->where('block_state', 'active')->update(['block_state' => 'released', 'released_at' => now(), 'status' => 'released', 'updated_by' => $actor->id]);
            PropertyChannelConnection::query()->where('property_id', $connection->property_id)->where('provider', $connection->provider)->update(['connection_status' => 'disconnected', 'status' => 'inactive', 'updated_by' => $actor->id]);
        });
    }

    private function createExport(Property $property, ?User $actor): PropertyCalendarExport
    {
        $token = Str::random(64);
        return PropertyCalendarExport::query()->create([
            'business_id' => $property->business_id, 'property_id' => $property->id,
            'plain_token' => $token, 'token_hash' => hash('sha256', $token), 'generated_at' => now(),
            'active_key' => 'active', 'status' => 'active', 'created_by' => $actor?->id, 'updated_by' => $actor?->id,
        ]);
    }
}
