<?php

namespace Tests\Feature\Presentation;

use App\Models\Business;
use App\Models\Property;
use Database\Seeders\PresentationDemoSeeder;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SandboxRefreshCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_clear_only_removes_the_sandbox_without_touching_live_records(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        $liveBusiness = Business::factory()->create(['is_test' => false, 'email' => 'live@example.test']);
        $liveProperty = Property::factory()->for($liveBusiness)->create(['is_test' => false, 'code' => 'LIVE-001']);

        $this->artisan('presentation:refresh', [
            '--execute' => true,
            '--clear-only' => true,
            '--confirm' => 'REFRESH-PRESENTATION-DATA',
        ])->assertSuccessful();

        $this->assertDatabaseHas('businesses', ['id' => $liveBusiness->id, 'is_test' => false]);
        $this->assertDatabaseHas('properties', ['id' => $liveProperty->id, 'is_test' => false]);
        $this->assertDatabaseMissing('businesses', ['is_test' => true]);
        $this->assertDatabaseMissing('properties', ['is_test' => true]);
    }

    public function test_rebuild_recreates_one_test_host_and_its_test_properties(): void
    {
        $this->artisan('presentation:refresh', [
            '--execute' => true,
            '--confirm' => 'REFRESH-PRESENTATION-DATA',
        ])->assertSuccessful();

        $host = Business::query()->where('is_test', true)->sole();

        $this->assertSame('Verified Shortlet Sandbox Host', $host->name);
        $this->assertSame('owner@coastlineresidences.test', $host->email);
        $this->assertSame(11, $host->properties()->where('is_test', true)->count());
        $this->assertSame(0, $host->properties()->where('is_test', false)->count());
    }
}
