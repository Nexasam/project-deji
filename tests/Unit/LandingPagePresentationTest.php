<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LandingPagePresentationTest extends TestCase
{
    public function test_landing_page_category_and_filter_rows_are_centered_on_larger_screens(): void
    {
        $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/components.css');

        $this->assertMatchesRegularExpression(
            '/@media\s*\(min-width:\s*768px\).*?\.cat-scroll\s*\{[^}]*justify-content:\s*center;[^}]*\}.*?\.filter-chips-row\s*\{[^}]*justify-content:\s*center;[^}]*\}/s',
            $css,
        );
    }

    public function test_step_eight_heading_has_no_literal_newline_escape_text(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/property-add-step8.blade.php');

        $this->assertStringNotContainsString('`n', $view);
    }

    public function test_landing_page_host_calls_to_action_use_registration(): void
    {
        $navbar = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/navbar.blade.php');
        $landingPage = file_get_contents(dirname(__DIR__, 2).'/resources/views/welcome.blade.php');

        $this->assertSame(2, substr_count($navbar, "route('register')"));
        $this->assertSame(2, substr_count($navbar, 'Get started'));
        $this->assertStringNotContainsString('List your property', $navbar);
        $this->assertStringContainsString('buttonText="Get started"', $landingPage);
        $this->assertStringContainsString(':buttonUrl="route(\'register\')"', $landingPage);
        $this->assertStringContainsString('secondaryButton="Get started"', $landingPage);
        $this->assertStringContainsString(':secondaryUrl="route(\'register\')"', $landingPage);
    }

    public function test_desktop_login_control_is_vertically_aligned(): void
    {
        $navbar = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/navbar.blade.php');

        $this->assertMatchesRegularExpression(
            '/<button\s+@click="\$store\.modals\.openLogin\(\)"\s+class="[^"]*inline-flex[^"]*items-center[^"]*justify-center[^"]*"/s',
            $navbar,
        );
    }

    public function test_mobile_login_uses_the_same_alignment_as_mobile_menu_links(): void
    {
        $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/components.css');

        $this->assertMatchesRegularExpression(
            '/\.mobile-menu\s+a,\s*\.mobile-menu\s+button\s*\{[^}]*align-items:\s*center;[^}]*padding:\s*14px\s+24px;[^}]*\}/s',
            $css,
        );
    }

    public function test_finance_uses_the_shared_workspace_background(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/owner/finance.blade.php');

        $this->assertStringContainsString('<body class="bg-slate-50', $view);
        $this->assertStringContainsString('<main class="mx-auto max-w-[1600px]', $view);
        $this->assertStringNotContainsString('bg-[#ECECEC]', $view);
        $this->assertStringContainsString('border border-slate-200 bg-white', $view);
        $this->assertStringNotContainsString("@push('styles')", $view);
    }

    public function test_property_portfolio_uses_four_columns_on_desktop(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/owner/properties/index.blade.php');

        $this->assertStringContainsString('xl:grid-cols-4', $view);
        $this->assertStringNotContainsString('xl:grid-cols-5', $view);
        $this->assertStringContainsString('data-testid="property-card"', $view);
        $this->assertStringContainsString('rounded-xl border border-slate-200 bg-white p-4', $view);
    }

    public function test_dashboard_real_properties_use_a_four_column_card_grid(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/dashboard.blade.php');

        $this->assertStringContainsString('data-testid="dashboard-property-grid"', $view);
        $this->assertStringContainsString('xl:grid-cols-4', $view);
        $this->assertStringNotContainsString('xl:grid-cols-5', $view);
        $this->assertStringContainsString('data-testid="dashboard-property-card"', $view);
        $this->assertStringContainsString('Real portfolio', $view);
        $this->assertStringNotContainsString("@include('partials.dashboard-content')", $view);
    }

    public function test_landing_hero_uses_the_bold_responsive_composition_and_keeps_a_backup(): void
    {
        $root = dirname(__DIR__, 2);
        $hero = file_get_contents($root.'/resources/views/components/hero.blade.php');
        $css = file_get_contents($root.'/resources/css/components.css');

        $this->assertFileExists($root.'/resources/views/components/backups/hero-2026-09-08.blade.php');
        $this->assertStringContainsString('hero-trust-card', $hero);
        $this->assertStringContainsString('hero-primary-image', $hero);
        $this->assertStringContainsString('clamp(3rem, 5.4vw, 5.25rem)', $css);
        $this->assertMatchesRegularExpression('/@media\s*\(max-width:\s*1023px\).*?\.hero-img-col\s*\{[^}]*display:\s*flex/s', $css);
        $this->assertDoesNotMatchRegularExpression('/\.hero-img-col\s*\{[^}]*display:\s*none\s*!important/s', $css);
    }

    public function test_auth_story_images_resolve_through_the_application_in_development(): void
    {
        $root = dirname(__DIR__, 2);
        $css = file_get_contents($root.'/resources/css/app.css');

        $this->assertStringNotContainsString("url('/hero1.jpg')", $css);
        foreach (['login', 'register', 'forgot-password'] as $viewName) {
            $view = file_get_contents($root."/resources/views/auth/{$viewName}.blade.php");
            $this->assertStringContainsString("asset('hero1.jpg')", $view);
            $this->assertStringContainsString("asset('hero2.jpg')", $view);
            $this->assertStringContainsString("asset('hero3.jpg')", $view);
        }
    }
}
