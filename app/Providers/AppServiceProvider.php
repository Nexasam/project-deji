<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Services\Payments\PlatformConfiguredPaymentGateway;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\UserRole;
use App\Support\ActiveBusinessContext;
use Illuminate\Support\ServiceProvider;
use App\Services\Notifications\PlatformMailConfigurator;
use App\Services\Storage\PlatformStorageConfigurator;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, PlatformConfiguredPaymentGateway::class);
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

    public function boot(PlatformMailConfigurator $mail, PlatformStorageConfigurator $storage): void
    {
        try {
            if (Schema::hasTable('platform_settings')) {
                $mail->apply();
                $storage->apply();
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
