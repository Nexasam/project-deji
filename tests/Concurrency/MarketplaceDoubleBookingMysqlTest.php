<?php

namespace Tests\Concurrency;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Services\Booking\BookingPricingService;
use App\Services\Booking\CreateMarketplaceBooking;
use App\Services\Business\BusinessOnboardingService;
use Carbon\CarbonImmutable;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Throwable;

class MarketplaceDoubleBookingMysqlTest extends TestCase
{
    public function test_two_simultaneous_requests_cannot_confirm_the_same_property_nights(): void
    {
        $this->assertSame('mysql', DB::getDriverName(), 'Run this dedicated proof with the MySQL test connection.');
        $this->assertSame('project_nexa_26_test', DB::getDatabaseName(), 'This test refuses to rebuild any database except project_nexa_26_test.');
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('The pcntl extension is required for the concurrent MySQL proof.');
        }

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => 'Concurrency Stays', 'country_code' => 'NG',
            'business_type' => 'serviced_apartments', 'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
        ]);
        $property = Property::factory()->for($business)->create([
            'capacity' => 4, 'default_nightly_price' => 50000, 'pricing_currency' => 'NGN',
        ]);
        $guests = User::factory()->count(2)->create();
        $quote = app(BookingPricingService::class)->quote(
            $property,
            CarbonImmutable::parse('2027-05-10'),
            CarbonImmutable::parse('2027-05-12'),
        );

        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $this->assertNotFalse($sockets);
        $children = [];

        foreach ($guests as $index => $guest) {
            $pid = pcntl_fork();
            $this->assertNotSame(-1, $pid, 'Could not fork a booking worker.');
            if ($pid === 0) {
                fclose($sockets[0]);
                fread($sockets[1], 1);
                DB::purge('mysql');
                try {
                    app(CreateMarketplaceBooking::class)->handle(
                        User::query()->findOrFail($guest->id),
                        Property::query()->findOrFail($property->id),
                        [
                            'arrival_date' => '2027-05-10', 'departure_date' => '2027-05-12',
                            'adult_count' => 2, 'child_count' => 0, 'guest_phone' => '+2348012345678',
                            'quoted_total' => $quote->decimal($quote->totalMinor),
                            'idempotency_key' => 'concurrent-request-'.$index,
                        ],
                    );
                    exit(0);
                } catch (ValidationException) {
                    exit(2);
                } catch (Throwable $error) {
                    fwrite(STDERR, $error::class.': '.$error->getMessage().PHP_EOL);
                    exit(3);
                }
            }
            $children[] = $pid;
        }

        fclose($sockets[1]);
        fwrite($sockets[0], 'xx');
        fclose($sockets[0]);
        $exitCodes = [];
        foreach ($children as $pid) {
            pcntl_waitpid($pid, $status);
            $exitCodes[] = pcntl_wexitstatus($status);
        }
        sort($exitCodes);

        DB::purge('mysql');
        $this->assertSame([0, 2], $exitCodes, 'Exactly one request must confirm and one must receive an availability rejection.');
        $this->assertSame(1, Booking::query()->where('property_id', $property->id)->where('status', 'confirmed')->count());
        $this->assertSame(2, $property->availabilityDays()->where('active_key', 'active')->count());
    }
}
