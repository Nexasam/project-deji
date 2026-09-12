<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncExternalCalendarConnection;
use App\Models\ExternalCalendarConnection;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('calendars:sync', function () {
    $count = 0;
    ExternalCalendarConnection::query()->where('status', 'active')->whereNotNull('credentials')->each(function ($connection) use (&$count) {
        SyncExternalCalendarConnection::dispatch($connection->id);
        $count++;
    });
    $this->info("Queued {$count} external calendar sync(s).");
})->purpose('Queue imports for all active external property calendars');

Schedule::command('calendars:sync')->everyFifteenMinutes()->withoutOverlapping();
