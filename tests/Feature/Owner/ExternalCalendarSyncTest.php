<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\ExternalCalendarConnection;
use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\PropertyCalendarExport;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Calendar\ImportExternalCalendar;
use App\Services\Calendar\ManageExternalCalendarConnection;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalCalendarSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_save_a_supported_feed_and_receives_an_encrypted_export_token(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();

        $this->actingAs($owner)->post(route('owner.properties.calendars.store', $property), [
            'provider' => 'airbnb',
            'feed_url' => 'https://www.airbnb.com/calendar/ical/123.ics?s=secret',
        ])->assertRedirect();

        $connection = ExternalCalendarConnection::query()->sole();
        $export = PropertyCalendarExport::query()->sole();
        $this->assertSame('https://www.airbnb.com/calendar/ical/123.ics?s=secret', $connection->credentials['feed_url']);
        $this->assertNull($connection->getRawOriginal('feed_url'));
        $this->assertNotSame($export->plain_token, $export->getRawOriginal('plain_token'));
        $this->assertSame(hash('sha256', $export->plain_token), $export->token_hash);
    }

    public function test_connection_rejects_unsafe_or_wrong_provider_urls(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();

        foreach (['http://www.airbnb.com/a.ics', 'https://127.0.0.1/a.ics', 'https://evil.test/a.ics'] as $url) {
            $this->actingAs($owner)->from(route('owner.properties.show', $property))->post(route('owner.properties.calendars.store', $property), [
                'provider' => 'airbnb', 'feed_url' => $url,
            ])->assertSessionHasErrors('feed_url');
        }

        $this->assertDatabaseCount('external_calendar_connections', 0);
    }

    public function test_sync_imports_updates_and_releases_external_blocks_idempotently(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();
        $connection = app(ManageExternalCalendarConnection::class)->save($property, $owner, 'airbnb', 'https://www.airbnb.com/calendar/ical/123.ics?s=secret');

        Http::fakeSequence()
            ->push($this->ical([['stay-1', '20261010', '20261013']]), 200)
            ->push($this->ical([['stay-1', '20261011', '20261014']]), 200)
            ->push($this->ical([]), 200);
        app(ImportExternalCalendar::class)->sync($connection, 'manual', $owner);
        $block = PropertyAvailabilityBlock::query()->sole();
        $this->assertSame('2026-10-10', $block->starts_on->toDateString());

        app(ImportExternalCalendar::class)->sync($connection->fresh(), 'manual', $owner);
        $this->assertDatabaseCount('property_availability_blocks', 1);
        $block->refresh();
        $this->assertSame('2026-10-11', $block->starts_on->toDateString());
        $this->assertSame('2026-10-14', $block->ends_on->toDateString());
        $this->assertSame('active', $block->block_state);

        app(ImportExternalCalendar::class)->sync($connection->fresh(), 'manual', $owner);
        $this->assertDatabaseHas('property_availability_blocks', ['id' => $block->id, 'block_state' => 'released']);
        $this->assertDatabaseCount('external_calendar_sync_runs', 3);
    }

    public function test_failed_fetch_preserves_existing_blocks_and_records_failure(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();
        $connection = app(ManageExternalCalendarConnection::class)->save($property, $owner, 'bookingcom', 'https://admin.booking.com/hotel/hoteladmin/ical.html?t=secret');
        PropertyAvailabilityBlock::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'external_calendar_connection_id' => $connection->id, 'source_type' => 'external_calendar',
            'source_reference' => 'existing', 'starts_on' => '2026-10-01', 'ends_on' => '2026-10-02',
            'blocks_booking' => true, 'validation_status' => 'validated', 'block_state' => 'active', 'status' => 'active',
        ]);
        Http::fake(['*' => Http::response('unavailable', 503)]);

        try { app(ImportExternalCalendar::class)->sync($connection, 'manual', $owner); } catch (\Throwable) {}

        $this->assertDatabaseHas('property_availability_blocks', ['source_reference' => 'existing', 'block_state' => 'active']);
        $this->assertDatabaseHas('external_calendar_sync_runs', ['sync_status' => 'failed']);
    }

    public function test_an_owner_cannot_manage_another_business_property(): void
    {
        [$owner] = $this->owner('First');
        [, $otherBusiness] = $this->owner('Second');
        $property = Property::factory()->for($otherBusiness)->create();

        $this->actingAs($owner)->post(route('owner.properties.calendars.store', $property), [
            'provider' => 'airbnb', 'feed_url' => 'https://www.airbnb.com/calendar/ical/123.ics',
        ])->assertNotFound();
    }

    public function test_owner_can_disable_a_connection_and_rotate_the_export_url(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();
        $manager = app(ManageExternalCalendarConnection::class);
        $connection = $manager->save($property, $owner, 'airbnb', 'https://www.airbnb.com/calendar/ical/123.ics');
        $oldExport = PropertyCalendarExport::query()->sole();
        PropertyAvailabilityBlock::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'external_calendar_connection_id' => $connection->id, 'source_type' => 'external_calendar',
            'source_reference' => 'external-1', 'starts_on' => '2026-10-01', 'ends_on' => '2026-10-02',
            'blocks_booking' => true, 'validation_status' => 'validated', 'block_state' => 'active', 'status' => 'active',
        ]);

        $this->actingAs($owner)->post(route('owner.properties.calendars.export.regenerate', $property))->assertRedirect();
        $this->assertDatabaseHas('property_calendar_exports', ['id' => $oldExport->id, 'status' => 'revoked', 'active_key' => null]);
        $this->assertDatabaseCount('property_calendar_exports', 2);

        $this->delete(route('owner.properties.calendars.destroy', [$property, $connection]))->assertRedirect();
        $this->assertDatabaseHas('external_calendar_connections', ['id' => $connection->id, 'status' => 'inactive']);
        $this->assertDatabaseHas('property_availability_blocks', ['source_reference' => 'external-1', 'block_state' => 'released']);
    }

    public function test_property_details_page_contains_owner_calendar_management_controls(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create();
        app(ManageExternalCalendarConnection::class)->save($property, $owner, 'airbnb', 'https://www.airbnb.com/calendar/ical/123.ics');

        $this->actingAs($owner)->get(route('owner.properties.show', $property))->assertOk()
            ->assertSee('Calendar sync')->assertSee('Sync now')->assertSee('Verified Shortlet export URL')
            ->assertSee('No API key is needed.');
    }

    /** @return array{User, Business} */
    private function owner(string $name = 'Nexa Stays'): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create();
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name, 'country_code' => 'NG', 'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
        ]);
        return [$user, $business];
    }

    /** @param array<int, array{string,string,string}> $events */
    private function ical(array $events): string
    {
        $body = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\n";
        foreach ($events as [$uid, $start, $end]) {
            $body .= "BEGIN:VEVENT\r\nUID:{$uid}\r\nDTSTART;VALUE=DATE:{$start}\r\nDTEND;VALUE=DATE:{$end}\r\nEND:VEVENT\r\n";
        }
        return $body."END:VCALENDAR\r\n";
    }
}
