<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\UserRole;
use App\Support\ActiveBusinessContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind a fallback ActiveBusinessContext so the app works without auth middleware.
        // The real binding is set per-request by EnsureActiveBusinessContext when auth is on.
        $this->app->bindIf(ActiveBusinessContext::class, function () {
            $business   = Business::first();
            $membership = BusinessMembership::first();
            $role       = UserRole::first();

            if (! $business || ! $membership || ! $role) {
                abort(503, 'No business data found. Run the seeders first.');
            }

            return new ActiveBusinessContext($business, $membership, $role);
        });
    }

    public function boot(): void
    {
        //
    }
}
