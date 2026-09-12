<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\PropertyAvailabilityDay;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OwnerBookingWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_booking_filters_are_database_backed_and_preserve_business_scope(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        [, $otherBusiness] = $this->ownerWithBusiness('Other Stays');
        $lekki = Property::factory()->for($business)->create(['name' => 'Lekki Residence']);
        $ikoyi = Property::factory()->for($business)->create(['name' => 'Ikoyi Residence']);
        $foreign = Property::factory()->for($otherBusiness)->create(['name' => 'Foreign Residence']);
        $visible = $this->booking($business, $lekki, 'VS-CONFIRMED', 'confirmed', 'marketplace');
        $this->booking($business, $ikoyi, 'VS-CANCELLED', 'cancelled', 'walk_in');
        $this->booking($otherBusiness, $foreign, 'VS-FOREIGN', 'confirmed', 'marketplace');

        $this->actingAs($owner)->get(route('owner.bookings', [
            'property' => $lekki->id,
            'status' => 'confirmed',
            'channel' => 'marketplace',
        ]))->assertOk()
            ->assertSee($visible->reference)
            ->assertDontSee('VS-CANCELLED')
            ->assertDontSee('VS-FOREIGN')
            ->assertSee('Lekki Residence')
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false);
    }

    public function test_owner_can_view_only_their_business_booking_details(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        [, $otherBusiness] = $this->ownerWithBusiness('Other Stays');
        $ownBooking = $this->booking($business, Property::factory()->for($business)->create(), 'VS-OWN', 'confirmed', 'marketplace');
        $foreignBooking = $this->booking($otherBusiness, Property::factory()->for($otherBusiness)->create(), 'VS-FOREIGN', 'confirmed', 'marketplace');

        $this->actingAs($owner)->get(route('owner.bookings.show', $ownBooking))
            ->assertOk()->assertSee('VS-OWN')->assertSee($ownBooking->guest->name);

        $this->actingAs($owner)->get(route('owner.bookings.show', $foreignBooking))
            ->assertNotFound();
    }

    public function test_calendar_renders_blocking_bookings_and_manual_blocks_for_the_selected_month(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Admiralty Suite']);
        $this->booking($business, $property, 'VS-SEPTEMBER', 'confirmed', 'marketplace', '2026-09-09', '2026-09-12');
        $this->booking($business, $property, 'VS-CANCELLED', 'cancelled', 'marketplace', '2026-09-15', '2026-09-17');
        PropertyAvailabilityBlock::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'source_type' => 'owner',
            'blocks_booking' => true,
            'starts_on' => '2026-09-20',
            'ends_on' => '2026-09-22',
            'validation_status' => 'valid',
            'block_state' => 'active',
            'reason' => 'Planned maintenance',
            'status' => 'active',
        ]);

        $this->actingAs($owner)->get(route('owner.calendar', ['month' => '2026-09', 'property' => $property->id]))
            ->assertOk()
            ->assertSee('September 2026')
            ->assertSee('VS-SEPTEMBER')
            ->assertSee('Planned maintenance')
            ->assertDontSee('VS-CANCELLED')
            ->assertSee('Connection pending');
    }

    public function test_owner_can_create_and_release_a_manual_availability_block(): void
    {
        Carbon::setTestNow('2026-09-09 09:00:00');
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Admiralty Suite']);

        $this->actingAs($owner)->post(route('owner.calendar.blocks.store'), [
            'property_id' => $property->id,
            'starts_on' => '2026-09-20',
            'ends_on' => '2026-09-22',
            'reason' => 'Planned maintenance',
            'month' => '2026-09',
        ])->assertRedirect(route('owner.calendar', ['month' => '2026-09', 'property' => $property->id]));

        $block = PropertyAvailabilityBlock::query()->sole();
        $this->assertSame($business->id, $block->business_id);
        $this->assertSame('active', $block->block_state);
        $this->assertSame($owner->id, $block->validated_by);

        $this->actingAs($owner)->delete(route('owner.calendar.blocks.destroy', $block), [
            'month' => '2026-09',
            'property' => $property->id,
        ])->assertRedirect(route('owner.calendar', ['month' => '2026-09', 'property' => $property->id]));

        $this->assertDatabaseHas('property_availability_blocks', [
            'id' => $block->id,
            'block_state' => 'released',
            'status' => 'released',
        ]);
    }

    public function test_manual_block_rejects_booking_conflicts_but_allows_same_day_turnover(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create();
        $this->booking($business, $property, 'VS-STAY', 'confirmed', 'marketplace', '2026-09-20', '2026-09-22');

        $this->actingAs($owner)->from(route('owner.calendar'))->post(route('owner.calendar.blocks.store'), [
            'property_id' => $property->id,
            'starts_on' => '2026-09-21',
            'ends_on' => '2026-09-23',
            'reason' => 'Conflict',
        ])->assertRedirect(route('owner.calendar'))->assertSessionHasErrors('starts_on');

        $this->actingAs($owner)->post(route('owner.calendar.blocks.store'), [
            'property_id' => $property->id,
            'starts_on' => '2026-09-22',
            'ends_on' => '2026-09-24',
            'reason' => 'After checkout',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('property_availability_blocks', 1);
    }

    public function test_owner_cannot_create_or_release_blocks_for_another_business(): void
    {
        [$owner] = $this->ownerWithBusiness('Nexa Stays');
        [, $otherBusiness] = $this->ownerWithBusiness('Other Stays');
        $foreignProperty = Property::factory()->for($otherBusiness)->create();
        $foreignBlock = PropertyAvailabilityBlock::query()->create([
            'business_id' => $otherBusiness->id, 'property_id' => $foreignProperty->id,
            'source_type' => 'owner', 'blocks_booking' => true, 'starts_on' => '2026-09-20',
            'ends_on' => '2026-09-22', 'block_state' => 'active', 'status' => 'active',
        ]);

        $this->actingAs($owner)->post(route('owner.calendar.blocks.store'), [
            'property_id' => $foreignProperty->id, 'starts_on' => '2026-09-24',
            'ends_on' => '2026-09-25', 'reason' => 'Not mine',
        ])->assertNotFound();

        $this->actingAs($owner)->delete(route('owner.calendar.blocks.destroy', $foreignBlock))
            ->assertNotFound();
        $this->assertSame('active', $foreignBlock->fresh()->block_state);
    }

    public function test_owner_can_reschedule_a_booking_and_replace_its_availability_claims(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create();
        $booking = $this->booking($business, $property, 'VS-MOVE', 'confirmed', 'marketplace', '2026-09-20', '2026-09-22');
        foreach (['2026-09-20', '2026-09-21'] as $date) {
            PropertyAvailabilityDay::query()->create([
                'business_id' => $business->id, 'property_id' => $property->id,
                'booking_id' => $booking->id, 'availability_date' => $date,
                'availability_state' => 'booked', 'source_type' => 'marketplace',
                'active_key' => 'active', 'allocated_at' => now(), 'status' => 'active',
            ]);
        }

        $this->actingAs($owner)->patch(route('owner.bookings.dates.update', $booking), [
            'arrival_date' => '2026-09-24',
            'departure_date' => '2026-09-27',
            'reason' => 'Guest requested new dates',
        ])->assertRedirect(route('owner.bookings.show', $booking));

        $booking->refresh();
        $this->assertSame('2026-09-24', $booking->arrival_date->toDateString());
        $this->assertSame('2026-09-27', $booking->departure_date->toDateString());
        $this->assertSame(3, $booking->availabilityDays()->where('active_key', 'active')->count());
        $this->assertSame(2, $booking->availabilityDays()->whereNull('active_key')->count());
        $change = $booking->dateChanges()->sole();
        $this->assertSame('2026-09-20', $change->previous_arrival_date->toDateString());
        $this->assertSame('2026-09-24', $change->new_arrival_date->toDateString());
        $this->assertSame('allocated', $change->availability_status);
    }

    public function test_owner_reschedule_rejects_conflicts_and_foreign_bookings(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        [, $otherBusiness] = $this->ownerWithBusiness('Other Stays');
        $property = Property::factory()->for($business)->create();
        $booking = $this->booking($business, $property, 'VS-MOVE', 'confirmed', 'marketplace', '2026-09-20', '2026-09-22');
        $this->booking($business, $property, 'VS-CONFLICT', 'confirmed', 'marketplace', '2026-09-25', '2026-09-27');
        $foreignBooking = $this->booking($otherBusiness, Property::factory()->for($otherBusiness)->create(), 'VS-FOREIGN', 'confirmed', 'marketplace');

        $this->actingAs($owner)->from(route('owner.bookings.show', $booking))
            ->patch(route('owner.bookings.dates.update', $booking), [
                'arrival_date' => '2026-09-24', 'departure_date' => '2026-09-26', 'reason' => 'Conflict',
            ])->assertSessionHasErrors('arrival_date');
        $this->assertSame('2026-09-20', $booking->fresh()->arrival_date->toDateString());

        $this->actingAs($owner)->patch(route('owner.bookings.dates.update', $foreignBooking), [
            'arrival_date' => '2026-09-24', 'departure_date' => '2026-09-26', 'reason' => 'Not mine',
        ])->assertNotFound();
    }

    private function booking(Business $business, Property $property, string $reference, string $status, string $source, string $arrival = '2026-09-09', string $departure = '2026-09-10'): Booking
    {
        return Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => User::factory(),
            'reference' => $reference,
            'status' => $status,
            'source' => $source,
            'arrival_date' => $arrival,
            'departure_date' => $departure,
            'currency' => 'NGN',
            'subtotal_amount' => 95000,
            'total_amount' => 99750,
        ]);
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }
}
