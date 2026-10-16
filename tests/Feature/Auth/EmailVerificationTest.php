<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_branded_verification_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create(['name' => 'Zainab Guest']);
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmailNotification::class, function ($notification) use ($user) {
            $message = $notification->toMail($user);

            $this->assertSame('Verify your email | Verified Shortlet', $message->subject);
            $this->assertSame('mail.auth.verify-email', $message->view['html']);
            $this->assertSame('mail.auth.verify-email-text', $message->view['text']);
            $this->assertSame('Zainab', $message->viewData['firstName']);

            return true;
        });
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
            absolute: false,
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('guest.dashboard', absolute: false).'?verified=1');
    }

    public function test_verified_individual_is_redirected_from_verification_prompt_to_guest_workspace(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertRedirect(route('guest.dashboard', absolute: false));
    }

    public function test_verified_business_account_is_redirected_to_business_onboarding(): void
    {
        $user = User::factory()->create([
            'account_type' => 'business',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertRedirect(route('owner.onboarding.business.create', absolute: false));
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')],
            absolute: false,
        );

        $this->actingAs($user)->get($verificationUrl)
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-invalid');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_expired_verification_link_offers_a_resend_instead_of_forbidden_page(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)],
            absolute: false,
        );

        $this->actingAs($user)->get($verificationUrl)
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-invalid');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
