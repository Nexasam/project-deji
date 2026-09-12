<?php

namespace App\Services\Calendar;

use App\Models\ExternalCalendarConnection;
use App\Models\ExternalCalendarSyncItem;
use App\Models\ExternalCalendarSyncRun;
use App\Models\PropertyAvailabilityBlock;
use App\Models\PropertyChannelConnection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportExternalCalendar
{
    public function __construct(private readonly FetchExternalCalendar $fetcher, private readonly IcalendarParser $parser) {}

    public function sync(ExternalCalendarConnection $connection, string $trigger = 'scheduled', ?User $actor = null): ExternalCalendarSyncRun
    {
        $run = ExternalCalendarSyncRun::query()->create([
            'business_id' => $connection->business_id, 'external_calendar_connection_id' => $connection->id,
            'direction' => 'import', 'trigger_type' => $trigger, 'sync_status' => 'running', 'started_at' => now(),
            'status' => 'active', 'created_by' => $actor?->id, 'updated_by' => $actor?->id,
        ]);

        try {
            $events = $this->parser->parse($this->fetcher->fetch($connection));
        } catch (Throwable $error) {
            $message = mb_substr($error->getMessage(), 0, 2000);
            $run->update(['sync_status' => 'failed', 'completed_at' => now(), 'failed_count' => 1, 'failure_reason' => $message]);
            $connection->update(['sync_status' => 'failed', 'last_synced_at' => now(), 'last_error' => $message, 'consecutive_failure_count' => $connection->consecutive_failure_count + 1]);
            throw $error;
        }

        return DB::transaction(function () use ($connection, $run, $events, $actor) {
            $connection = ExternalCalendarConnection::query()->lockForUpdate()->findOrFail($connection->id);
            $seen = []; $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0];
            foreach ($events as $event) {
                $seen[] = $event['uid'];
                $block = PropertyAvailabilityBlock::query()->where('external_calendar_connection_id', $connection->id)->where('source_reference', $event['uid'])->first();
                $operation = 'skipped';
                if ($event['cancelled']) {
                    if ($block && $block->block_state === 'active') {
                        $block->update(['block_state' => 'released', 'released_at' => now(), 'status' => 'released', 'updated_by' => $actor?->id]);
                        $operation = 'released'; $counts['updated']++;
                    } else $counts['skipped']++;
                } elseif (! $block) {
                    $block = PropertyAvailabilityBlock::query()->create($this->blockData($connection, $event, $actor));
                    $operation = 'created'; $counts['created']++;
                } else {
                    $changed = $block->starts_on->toDateString() !== $event['starts_on'] || $block->ends_on->toDateString() !== $event['ends_on'] || $block->block_state !== 'active';
                    if ($changed) {
                        $block->update($this->blockData($connection, $event, $actor) + ['released_at' => null]);
                        $operation = 'updated'; $counts['updated']++;
                    } else $counts['skipped']++;
                }
                ExternalCalendarSyncItem::query()->create([
                    'business_id' => $connection->business_id, 'sync_run_id' => $run->id, 'external_event_id' => $event['uid'],
                    'operation' => $operation, 'validation_status' => 'validated', 'result_status' => 'complete',
                    'source_type' => 'property_availability_block', 'source_id' => $block?->id,
                    'payload_hash' => hash('sha256', json_encode($event)), 'payload' => $event, 'processed_at' => now(),
                    'status' => 'active', 'created_by' => $actor?->id, 'updated_by' => $actor?->id,
                ]);
            }
            $missing = PropertyAvailabilityBlock::query()->where('external_calendar_connection_id', $connection->id)->where('block_state', 'active')
                ->when($seen, fn ($query) => $query->whereNotIn('source_reference', $seen))->get();
            foreach ($missing as $block) {
                $block->update(['block_state' => 'released', 'released_at' => now(), 'status' => 'released', 'updated_by' => $actor?->id]);
                $counts['updated']++;
            }
            $run->update([
                'sync_status' => 'complete', 'completed_at' => now(), 'received_count' => count($events),
                'created_count' => $counts['created'], 'updated_count' => $counts['updated'], 'skipped_count' => $counts['skipped'],
            ]);
            $connection->update(['sync_status' => 'connected', 'last_synced_at' => now(), 'last_imported_at' => now(), 'last_error' => null, 'consecutive_failure_count' => 0]);
            PropertyChannelConnection::query()->where('property_id', $connection->property_id)->where('provider', $connection->provider)->update(['connection_status' => 'connected', 'updated_by' => $actor?->id]);
            return $run->fresh();
        });
    }

    private function blockData(ExternalCalendarConnection $connection, array $event, ?User $actor): array
    {
        return [
            'business_id' => $connection->business_id, 'property_id' => $connection->property_id,
            'external_calendar_connection_id' => $connection->id, 'source_type' => 'external_calendar',
            'source_reference' => $event['uid'], 'blocks_booking' => true, 'starts_on' => $event['starts_on'], 'ends_on' => $event['ends_on'],
            'validation_status' => 'validated', 'validated_by' => $actor?->id, 'validated_at' => now(),
            'block_state' => 'active', 'reason' => ucfirst($connection->provider).' calendar reservation', 'status' => 'active',
            'created_by' => $actor?->id, 'updated_by' => $actor?->id,
        ];
    }
}
