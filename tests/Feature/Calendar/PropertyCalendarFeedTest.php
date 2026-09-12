<?php

namespace Tests\Feature\Calendar;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\PropertyCalendarExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PropertyCalendarFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_token_exports_native_blocks_without_guest_data_or_imported_events(): void
    {
        $business = Business::factory()->create();
        $property = Property::factory()->for($business)->create();
        Booking::factory()->for($business)->for($property)->create([
            'reference' => 'PRIVATE-REF', 'arrival_date' => '2026-12-10', 'departure_date' => '2026-12-13',
            'status' => BookingStatus::Confirmed, 'payment_status' => BookingPaymentStatus::Paid,
        ]);
        foreach ([['owner', 'maintenance'], ['external_calendar', 'external-secret']] as [$source, $reference]) {
            PropertyAvailabilityBlock::query()->create([
                'business_id' => $business->id, 'property_id' => $property->id, 'source_type' => $source,
                'source_reference' => $reference, 'starts_on' => '2026-12-20', 'ends_on' => '2026-12-22',
                'blocks_booking' => true, 'validation_status' => 'validated', 'block_state' => 'active', 'status' => 'active',
            ]);
        }
        $token = Str::random(64);
        $export = PropertyCalendarExport::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id, 'plain_token' => $token,
            'token_hash' => hash('sha256', $token), 'generated_at' => now(), 'active_key' => 'active', 'status' => 'active',
        ]);

        $response = $this->get(route('property-calendar-feed', [$export, $token]));
        $response->assertOk()->assertHeader('content-type', 'text/calendar; charset=UTF-8')
            ->assertSee('DTSTART;VALUE=DATE:20261210', false)
            ->assertSee('DTSTART;VALUE=DATE:20261220', false)
            ->assertDontSee('PRIVATE-REF')->assertDontSee('external-secret');

        $this->get(route('property-calendar-feed', [$export, 'wrong-token']))->assertNotFound();
    }
}
