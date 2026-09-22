<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GuestBookingPresentationTest extends TestCase
{
    public function test_guest_booking_list_exposes_rich_trip_information(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/guest/bookings/index.blade.php');

        $this->assertStringContainsString('Explore more stays', $view);
        $this->assertStringContainsString('Payment:', $view);
        $this->assertStringContainsString('View booking', $view);
        $this->assertStringContainsString("property->media", $view);
    }

    public function test_guest_booking_detail_has_price_property_and_cancellation_information(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/guest/bookings/show.blade.php');

        $this->assertStringContainsString('Stay details', $view);
        $this->assertStringContainsString('About the property', $view);
        $this->assertStringContainsString('Payment summary', $view);
        $this->assertStringContainsString('Stay subtotal', $view);
        $this->assertStringNotContainsString('Service fee', $view);
        $this->assertStringContainsString('cancelOpen=true', $view);
        $this->assertStringContainsString('within 48 hours of check-in', $view);
        $this->assertStringContainsString('Yes, cancel booking', $view);
    }
}
