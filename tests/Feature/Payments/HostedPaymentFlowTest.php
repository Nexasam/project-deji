<?php

namespace Tests\Feature\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Enums\IdentityVerificationStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PlatformSetting;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Database\Seeders\PlatformAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use RuntimeException;

class HostedPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_paystack_checkout_is_only_confirmed_after_provider_verification(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->setting('payments.paystack_secret_key', 'sk_test_paystack');

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.test/session', 'access_code' => 'access', 'reference' => 'VS-PAYSTACK-REF'],
            ]),
        ]);

        $response = $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout(['payment_provider' => 'paystack']));
        $response->assertRedirect('https://checkout.paystack.test/session');
        $booking = Booking::query()->sole();
        $this->assertSame('awaiting_payment', $booking->status->value);
        $this->assertSame('pending', Payment::query()->sole()->status->value);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['id' => 5544, 'reference' => 'VS-PAYSTACK-REF', 'status' => 'success', 'amount' => 28500000, 'currency' => 'NGN'],
            ]),
        ]);
        $this->get(route('payments.paystack.callback', ['reference' => 'VS-PAYSTACK-REF']))
            ->assertRedirect(route('guest.bookings.confirmation', $booking));

        $this->assertSame('confirmed', $booking->refresh()->status->value);
        $this->assertSame('paid', $booking->payment_status->value);
        $this->assertSame('completed', Payment::query()->sole()->status->value);
        $this->assertSame(3, $booking->availabilityDays()->count());
        $this->assertDatabaseHas('booking_financial_allocations', ['payment_id' => Payment::query()->sole()->id, 'allocation_type' => 'platform_fee']);
        $this->assertDatabaseHas('notifications', ['type' => 'platform_payment_received']);
    }

    public function test_flutterwave_checkout_is_only_confirmed_after_verified_callback(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->setting('payments.flutterwave_secret_key', 'flw_test_secret');

        Http::fake(['api.flutterwave.com/v3/payments' => Http::response(['status' => 'success', 'data' => ['link' => 'https://checkout.flutterwave.test/session']])]);
        $response = $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout(['payment_provider' => 'flutterwave', 'idempotency_key' => 'flutterwave-checkout']));
        $response->assertRedirect('https://checkout.flutterwave.test/session');
        $booking = Booking::query()->sole();

        Http::fake(['api.flutterwave.com/v3/transactions/*/verify' => Http::response([
            'status' => 'success',
            'data' => ['id' => 9911, 'tx_ref' => $booking->reference, 'status' => 'successful', 'amount' => 285000, 'currency' => 'NGN'],
        ])]);
        $this->get(route('payments.flutterwave.callback', ['transaction_id' => 9911, 'tx_ref' => $booking->reference, 'status' => 'successful']))
            ->assertRedirect(route('guest.bookings.confirmation', $booking));

        $this->assertSame('confirmed', $booking->refresh()->status->value);
        $this->assertSame('completed', Payment::query()->sole()->status->value);
    }

    public function test_paystack_webhook_requires_a_valid_signature_and_is_idempotent(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $secret = 'sk_test_paystack_webhook';
        $this->setting('payments.paystack_secret_key', $secret);

        Http::fake(['api.paystack.co/transaction/initialize' => Http::response([
            'status' => true,
            'data' => ['authorization_url' => 'https://checkout.paystack.test/session', 'reference' => 'VS-WEBHOOK-REF'],
        ])]);
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout(['idempotency_key' => 'paystack-webhook']))
            ->assertRedirect('https://checkout.paystack.test/session');

        Http::fake(['api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'data' => ['id' => 777, 'reference' => 'VS-WEBHOOK-REF', 'status' => 'success', 'amount' => 28500000, 'currency' => 'NGN'],
        ])]);
        $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'VS-WEBHOOK-REF']], JSON_THROW_ON_ERROR);

        $this->call('POST', '/webhooks/paystack', [], [], [], ['CONTENT_TYPE' => 'application/json'], $payload)->assertUnauthorized();

        $signature = hash_hmac('sha512', $payload, $secret);
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => $signature];
        $this->call('POST', '/webhooks/paystack', [], [], [], $headers, $payload)->assertOk();
        $this->call('POST', '/webhooks/paystack', [], [], [], $headers, $payload)->assertOk();

        $booking = Booking::query()->sole();
        $this->assertSame('confirmed', $booking->status->value);
        $this->assertSame(2, $booking->financialAllocations()->count());
        $this->assertSame(3, $booking->availabilityDays()->count());
    }

    public function test_paystack_webhook_always_uses_the_active_api_secret_for_signature_verification(): void
    {
        $secret = 'sk_test_active-paystack-secret';
        $this->setting('payments.paystack_test_secret_key', $secret);
        $this->setting('payments.paystack_test_webhook_secret', 'deprecated-and-must-not-be-used');
        $payload = json_encode(['event' => 'unhandled.test', 'data' => []], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha512', $payload, $secret);

        $this->call('POST', '/webhooks/paystack', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
        ], $payload)->assertOk();
    }

    public function test_flutterwave_webhook_rejects_an_invalid_hash(): void
    {
        $this->setting('payments.flutterwave_webhook_secret', 'expected-hash');

        $this->postJson('/webhooks/flutterwave', ['event' => 'charge.completed', 'data' => ['id' => 9]], ['verif-hash' => 'wrong-hash'])
            ->assertUnauthorized();
    }

    public function test_selected_payment_environment_uses_only_its_provider_credentials(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->setting('payments.gateway_execution_mode', 'live');
        $this->setting('payments.paystack_test_secret_key', 'sk_test_never-use');
        $this->setting('payments.paystack_live_secret_key', 'sk_live_selected');

        Http::fake(function ($request) {
            $this->assertSame('Bearer sk_live_selected', $request->header('Authorization')[0] ?? null);

            return Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.test/live-session', 'reference' => 'VS-LIVE-REF'],
            ]);
        });

        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout([
            'idempotency_key' => 'live-environment-checkout',
        ]))->assertRedirect('https://checkout.paystack.test/live-session');
    }

    public function test_platform_test_mode_can_complete_internal_checkout_without_provider_credentials(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);

        $this->actingAs($guest)
            ->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout([
                'payment_provider' => 'flutterwave',
                'idempotency_key' => 'internal-test-checkout',
            ]))
            ->assertRedirect();

        $payment = Payment::query()->sole();
        $this->assertSame('completed', $payment->status->value);
        $this->assertSame('flutterwave', $payment->provider->value);
        $this->assertSame('internal_test', data_get($payment->provider_metadata, 'checkout_path'));
    }

    public function test_unavailable_payment_provider_returns_graceful_feedback_and_alerts_platform_owner(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Verified]);
        $this->app->instance(PaymentGateway::class, new class implements PaymentGateway
        {
            public function charge(string $reference, int $amountMinor, string $currency, array $context = []): \App\Data\PaymentResult
            {
                throw new RuntimeException('Provider credentials are unavailable.');
            }
        });

        $this->actingAs($guest)
            ->from('/stays/lekki-admiralty-waterfront/checkout')
            ->post('/stays/lekki-admiralty-waterfront/checkout', $this->checkout(['idempotency_key' => 'provider-attention']))
            ->assertRedirect('/stays/lekki-admiralty-waterfront/checkout')
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseHas('notifications', [
            'type' => 'platform.provider_attention',
            'title' => 'Payment provider needs attention',
        ]);
        $this->assertDatabaseCount('bookings', 0);
    }

    private function checkout(array $overrides = []): array
    {
        return array_merge([
            'arrival_date' => '2026-11-10', 'departure_date' => '2026-11-13',
            'adult_count' => 2, 'child_count' => 1, 'guest_phone' => '+2348012345678',
            'quoted_total' => 285000, 'idempotency_key' => 'hosted-checkout',
            'payment_option' => 'full', 'payment_provider' => 'paystack',
        ], $overrides);
    }

    private function setting(string $key, mixed $value): void
    {
        [$group] = explode('.', $key, 2);
        PlatformSetting::query()->create(['group_key' => $group, 'key' => $key, 'value' => $value, 'value_type' => 'secret', 'label' => $key, 'description' => null]);
    }
}
