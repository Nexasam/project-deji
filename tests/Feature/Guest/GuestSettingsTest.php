<?php

namespace Tests\Feature\Guest;

use App\Enums\IdentityVerificationStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_settings_requires_authentication(): void
    {
        $this->get(route('guest.settings.edit'))->assertRedirect('/login');
    }

    public function test_guest_can_view_and_update_settings(): void
    {
        $guest = User::factory()->create(['phone_number' => null]);

        $this->actingAs($guest)
            ->get(route('guest.settings.edit'))
            ->assertOk()
            ->assertSee('Your details help hosts confirm and verify your bookings.')
            ->assertSeeText('Account & security');

        $this->patch(route('guest.settings.update'), [
            'name' => 'Tunde Balogun',
            'email' => $guest->email,
            'phone_number' => '+234 801 234 5678',
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('guest.settings.edit'));

        $this->assertSame('Tunde Balogun', $guest->refresh()->name);
        $this->assertSame('+2348012345678', $guest->phone_number);
    }

    public function test_guest_can_verify_nin_from_the_inline_settings_action(): void
    {
        $guest = User::factory()->create(['identity_verification_status' => IdentityVerificationStatus::Unverified]);

        $this->actingAs($guest)->patch(route('guest.settings.update'), [
            'name' => $guest->name,
            'email' => $guest->email,
            'phone_number' => $guest->phone_number,
            'nin' => '12345678901',
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('guest.settings.edit'))
            ->assertSessionHas('status', 'identity-verified');

        $guest->refresh();
        $this->assertSame(IdentityVerificationStatus::Verified, $guest->identity_verification_status);
        $this->assertSame('8901', data_get($guest->identity_verification, 'nin_last4'));
        $this->assertArrayNotHasKey('nin', $guest->identity_verification ?? []);
    }
}
