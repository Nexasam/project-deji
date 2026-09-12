<?php

namespace App\Jobs;

use App\Models\ExternalCalendarConnection;
use App\Services\Calendar\ImportExternalCalendar;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncExternalCalendarConnection implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 840;
    public int $tries = 3;
    public array $backoff = [60, 300];

    public function __construct(public readonly string $connectionId) {}

    public function uniqueId(): string { return $this->connectionId; }

    public function handle(ImportExternalCalendar $importer): void
    {
        $connection = ExternalCalendarConnection::query()->whereKey($this->connectionId)->where('status', 'active')->first();
        if ($connection?->inboundFeedUrl()) $importer->sync($connection, 'scheduled');
    }
}
