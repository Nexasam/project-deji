<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Property;
use App\Models\User;
use App\Services\Maintenance\PurgeNonTestWorkData;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class NonTestWorkDataCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_preserves_test_records_and_global_configuration(): void
    {
        $this->seed(AccessControlSeeder::class);
        $permissionCount = DB::table('permissions')->count();

        $testBusiness = Business::factory()->create(['is_test' => true, 'email' => 'sandbox@example.test']);
        $liveBusiness = Business::factory()->create(['is_test' => false, 'email' => 'live@example.com']);
        $testProperty = Property::factory()->for($testBusiness)->create(['is_test' => true]);
        $liveProperty = Property::factory()->for($liveBusiness)->create(['is_test' => false]);
        $testUser = User::factory()->create(['email' => 'guest@example.test']);
        $liveUser = User::factory()->create(['email' => 'guest@example.com']);

        $plan = app(PurgeNonTestWorkData::class)->preview();

        $this->assertContains($liveBusiness->id, $plan['targets']['businesses']);
        $this->assertContains($liveUser->id, $plan['targets']['users']);
        $this->assertNotContains($testBusiness->id, $plan['targets']['businesses']);
        $this->assertNotContains($testUser->id, $plan['targets']['users']);

        app(PurgeNonTestWorkData::class)->execute();

        $this->assertDatabaseHas('businesses', ['id' => $testBusiness->id, 'is_test' => true]);
        $this->assertDatabaseHas('properties', ['id' => $testProperty->id, 'is_test' => true]);
        $this->assertDatabaseHas('users', ['id' => $testUser->id]);
        $this->assertDatabaseMissing('businesses', ['id' => $liveBusiness->id]);
        $this->assertDatabaseMissing('properties', ['id' => $liveProperty->id]);
        $this->assertDatabaseMissing('users', ['id' => $liveUser->id]);
        $this->assertSame($permissionCount, DB::table('permissions')->count());
    }
}
