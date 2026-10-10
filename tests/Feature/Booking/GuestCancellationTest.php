<?php

namespace Tests\Feature\Booking;

use App\Enums\IdentityVerificationStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\PlatformSetting;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GuestCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_guest_cancels_outside_48_hours_and_receives_a_refund(): void
    {
        Carbon::setTestNow('2026-11-01 10:00');
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', ['arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12', 'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678', 'quoted_total' => 190000, 'idempotency_key' => 'cancel-me']);
        $booking = Booking::sole();
        $this->post("/guest/bookings/{$booking->id}/cancel", ['reason' => 'Plans changed'])->assertRedirect(route('guest.bookings.show', $booking));
        $booking->refresh();
        $this->assertSame('cancelled', $booking->status->value);
        $this->assertSame('paid', $booking->payment_status->value);
        $this->assertSame(0, $booking->availabilityDays()->where('active_key', 'active')->count());
        $this->assertSame(1, $booking->cancellations()->count());
        $this->assertSame(2, $booking->payments()->count());
    }

    public function test_guest_cannot_cancel_someone_elses_booking(): void
    {
        $booking = Booking::factory()->create(['status' => 'confirmed']);
        $this->actingAs(User::factory()->create())->post("/guest/bookings/{$booking->id}/cancel")->assertNotFound();
    }

    public function test_eligible_part_payment_refund_deducts_configured_penalty(): void
    {
        Carbon::setTestNow('2026-11-01 10:00');
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        PlatformSetting::query()->create(['group_key' => 'payments', 'key' => 'payments.partial_refund_penalty_percentage', 'value' => 10, 'value_type' => 'decimal', 'label' => 'Part-payment cancellation penalty']);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', [
            'arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12',
            'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678',
            'quoted_total' => 190000, 'idempotency_key' => 'part-refund', 'payment_option' => 'installment',
        ]);
        $booking = Booking::query()->sole();
        $captured = (float) $booking->payments()->where('purpose', 'deposit')->sole()->amount;

        $this->post(route('guest.bookings.cancel', $booking), ['reason' => 'Plans changed'])->assertRedirect();

        $cancellation = $booking->cancellations()->sole();
        $this->assertSame(round($captured * 0.9, 2), (float) $cancellation->refund_amount);
        $this->assertSame(round($captured * 0.1, 2), (float) $cancellation->cancellation_fee);
        $this->assertSame(10.0, (float) $cancellation->policy_snapshot['penalty_percentage']);
    }

    public function test_refund_cutoff_uses_the_listing_check_in_time(): void
    {
        Carbon::setTestNow('2026-11-08 16:00:00');
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', [
            'arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12',
            'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678', 'quoted_total' => 190000, 'idempotency_key' => 'late-checkin-cancel',
        ]);
        $booking = Booking::query()->sole();
        $booking->property->marketplaceListing()->update(['check_in_time' => '18:00']);

        $this->post(route('guest.bookings.cancel', $booking), ['reason' => 'Plans changed'])->assertRedirect();

        $this->assertSame('paid', $booking->fresh()->payment_status->value);
        $this->assertTrue((bool) $booking->cancellations()->sole()->policy_snapshot['refundable']);
    }
}
