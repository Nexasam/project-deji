<?php

use App\Jobs\SyncExternalCalendarConnection;
use App\Models\Booking;
use App\Models\DocumentVersion;
use App\Models\ExternalCalendarConnection;
use App\Models\Notification;
use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

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

Artisan::command('calendars:notify-stale', function (ProductNotificationService $notifications) {
    $count = 0;
    ExternalCalendarConnection::query()->with(['business', 'property'])
        ->where('status', 'active')->where('sync_status', '!=', 'stale_alerted')
        ->where(function ($query): void {
            $query->where('last_synced_at', '<', now()->subMinutes(45))
                ->orWhere(function ($neverSynced): void {
                    $neverSynced->whereNull('last_synced_at')->where('created_at', '<', now()->subMinutes(45));
                });
        })->each(function (ExternalCalendarConnection $connection) use ($notifications, &$count): void {
            $connection->update(['sync_status' => 'stale_alerted']);
            $data = ['url' => route('owner.properties.show', $connection->property).'#calendar-sync', 'property_id' => $connection->property_id, 'connection_id' => $connection->id];
            $title = 'Calendar sync is stale';
            $message = ucfirst($connection->provider)." calendar for {$connection->property->name} has not synchronized in over 45 minutes. Review it before accepting offline bookings.";
            $notifications->businessOwners($connection->business, 'calendar_sync_stale', $title, $message, $data);
            $notifications->platformAdmins($connection->business, 'calendar_sync_stale', $title, $message, $data);
            $count++;
        });
    $this->info("Flagged {$count} stale external calendar connection(s).");
})->purpose('Alert owners and administrators when an external calendar stops synchronizing');

Schedule::command('calendars:notify-stale')->everyFifteenMinutes()->withoutOverlapping();

Artisan::command('operations:notify-overdue', function (ProductNotificationService $notifications) {
    $count = 0;
    OperationalTask::query()->with(['business', 'property', 'assignedEmployee.businessMembership.user'])
        ->whereNull('escalated_at')->whereNotNull('due_at')->where('due_at', '<', now())
        ->whereNotIn('status', ['completed', 'cancelled'])->each(function (OperationalTask $task) use ($notifications, &$count): void {
            $task->update(['escalated_at' => now()]);
            $message = "{$task->title} at {$task->property->name} is overdue.";
            $notifications->businessOwners($task->business, 'task_overdue', 'Operational task overdue', $message, ['url' => route('owner.operations'), 'task_id' => $task->id]);
            if ($task->assignedEmployee?->businessMembership?->user) {
                $notifications->user($task->assignedEmployee->businessMembership->user, $task->business, 'task_overdue', 'Your task is overdue', $message, ['url' => route('staff.tasks.index'), 'task_id' => $task->id]);
            }
            $count++;
        });
    $this->info("Escalated {$count} overdue operational task(s).");
})->purpose('Create one owner/staff alert when an operational task becomes overdue');

Schedule::command('operations:notify-overdue')->everyFiveMinutes()->withoutOverlapping();

Artisan::command('bookings:notify-upcoming-arrivals', function (ProductNotificationService $notifications) {
    $count = 0;
    Booking::query()->with(['guest', 'business.memberships.roles.role', 'property.marketplaceListing'])
        ->where('status', 'confirmed')->whereDate('arrival_date', today()->addDay())
        ->each(function (Booking $booking) use ($notifications, &$count): void {
            $checkIn = substr($booking->property->marketplaceListing?->check_in_time ?: '14:00', 0, 5);
            $data = ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id];
            if (! Notification::query()->where('user_id', $booking->guest_user_id)->where('type', 'arrival_reminder')->where('data->booking_id', $booking->id)->exists()) {
                $notifications->user($booking->guest, $booking->business, 'arrival_reminder', 'Your stay starts tomorrow', "Check-in for {$booking->property->name} begins at {$checkIn}. Review your booking details before travelling.", $data);
                $count++;
            }
            $ownerIds = $booking->business->memberships()->where('status', 'active')
                ->whereHas('roles', fn ($query) => $query->where('status', 'active')
                    ->whereHas('role', fn ($role) => $role->where('system_key', 'business_owner')))
                ->pluck('user_id');
            User::query()->whereIn('id', $ownerIds)->each(function ($owner) use ($booking, $notifications, &$count): void {
                if (Notification::query()->where('user_id', $owner->id)->where('type', 'arrival_reminder')->where('data->booking_id', $booking->id)->exists()) {
                    return;
                }
                $notifications->user($owner, $booking->business, 'arrival_reminder', 'Guest arrival tomorrow', "{$booking->guest->name} is due at {$booking->property->name} tomorrow. Confirm preparation and access handover.", ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id]);
                $count++;
            });
        });
    $this->info("Created {$count} upcoming-arrival reminder(s).");
})->purpose('Notify guests and owners one day before confirmed arrivals');

Schedule::command('bookings:notify-upcoming-arrivals')->hourly()->withoutOverlapping();

Artisan::command('documents:migrate-private {--dry-run}', function () {
    $versions = DocumentVersion::query()
        ->where('storage_disk', 'public')
        ->whereHas('document', fn ($query) => $query->where('owner_type', Property::class))
        ->get();

    if ($this->option('dry-run')) {
        $this->info("{$versions->count()} property document version(s) would be migrated.");

        return self::SUCCESS;
    }

    $migrated = 0;
    foreach ($versions as $version) {
        if (! Storage::disk('public')->exists($version->storage_path)) {
            $this->warn("Skipped missing file: {$version->storage_path}");

            continue;
        }

        $stream = Storage::disk('public')->readStream($version->storage_path);
        if ($stream === false || ! Storage::disk('local')->writeStream($version->storage_path, $stream)) {
            if (is_resource($stream)) {
                fclose($stream);
            }
            $this->error("Could not copy: {$version->storage_path}");

            continue;
        }
        if (is_resource($stream)) {
            fclose($stream);
        }

        DB::transaction(function () use ($version): void {
            DocumentVersion::query()->whereKey($version->id)->update(['storage_disk' => 'local']);
        });
        Storage::disk('public')->delete($version->storage_path);
        $migrated++;
    }

    $this->info("Migrated {$migrated} property document version(s) to private storage.");

    return self::SUCCESS;
})->purpose('Move existing internal property documents from public to private storage');
