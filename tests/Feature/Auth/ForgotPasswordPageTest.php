<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class ForgotPasswordPageTest extends TestCase
{
    public function test_forgot_password_page_uses_the_project_nexus_authentication_design(): void
    {
        $response = $this->get('/forgot-password');

        $response
            ->assertOk()
            ->assertSee('Reset your password')
            ->assertSee('Send reset link')
            ->assertSee('Back to sign in')
            ->assertSee('One platform. Every hospitality journey.')
            ->assertSee('name="email"', false)
            ->assertSee('action="'.route('password.email').'"', false);
    }
}
