<?php

namespace Tests\Feature\Console;

use App\Jobs\SyncExternalCalendarConnection;
use App\Models\Business;
use App\Models\ExternalCalendarConnection;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SyncExternalCalendarsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_only_queues_active_configured_connections(): void
    {
        Queue::fake();
        $business = Business::factory()->create();
        $property = Property::factory()->for($business)->create();
        foreach ([['active', ['feed_url' => 'https://www.airbnb.com/a.ics']], ['inactive', ['feed_url' => 'https://www.airbnb.com/b.ics']], ['active', null]] as $index => [$status, $credentials]) {
            ExternalCalendarConnection::query()->create([
                'business_id' => $business->id, 'property_id' => $property->id, 'provider' => 'provider-'.$index,
                'external_calendar_id' => 'calendar-'.$index, 'credentials' => $credentials, 'status' => $status,
            ]);
        }

        $this->artisan('calendars:sync')->assertSuccessful()->expectsOutput('Queued 1 external calendar sync(s).');
        Queue::assertPushed(SyncExternalCalendarConnection::class, 1);
    }
}
