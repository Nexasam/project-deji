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
        $this->assertStringContainsString('overflow-hidden rounded-lg border border-slate-200 bg-white', $view);
        $this->assertStringContainsString('Superhost', $view);
        $this->assertStringContainsString('Superhost eligible', $view);
        $this->assertStringContainsString('Reviewing', $view);
        $this->assertStringContainsString('Needs fixes', $view);
        $this->assertStringNotContainsString('Verified</span>', $view);
    }

    public function test_dual_access_users_have_guest_owner_workspace_switches(): void
    {
        $navbar = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/navbar.blade.php');
        $sidebar = file_get_contents(dirname(__DIR__, 2).'/resources/views/partials/sidebar-inner.blade.php');

        $this->assertStringContainsString('Business', $navbar);
        $this->assertStringContainsString("route('owner.entry')", $navbar);
        $this->assertStringContainsString("route('guest.bookings.index')", $navbar);
        $this->assertStringContainsString('$isDashboardActive', $navbar);
        $this->assertStringContainsString('$isBookingsActive', $navbar);
        $this->assertStringContainsString('$isMessagesActive', $navbar);
        $this->assertStringContainsString('Guest dashboard', $sidebar);
        $this->assertStringContainsString("route('guest.dashboard')", $sidebar);

        $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/components.css');
        $this->assertStringContainsString('.mobile-menu a.active', $css);
    }

    public function test_marketplace_cards_fall_back_to_uploaded_storage_images(): void
    {
        $landing = file_get_contents(dirname(__DIR__, 2).'/resources/views/welcome.blade.php');
        $detail = file_get_contents(dirname(__DIR__, 2).'/resources/views/marketplace/show.blade.php');
        $card = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/property-card.blade.php');
        $tile = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/marketplace-property-tile.blade.php');

        $this->assertStringContainsString("'/storage/'.ltrim(\$coverMedia->storage_path", $tile);
        $this->assertStringContainsString(':image="$coverImage"', $tile);
        $this->assertStringContainsString("'/storage/'.ltrim(\$coverMedia->storage_path", $detail);
        $this->assertStringContainsString("target.src='/image.png'", $detail);
        $this->assertStringContainsString('AI Insights', $card);
        $this->assertStringContainsString('open-property-insights', $card);
        $this->assertStringNotContainsString('aria-disabled="true"', $card);
        $this->assertStringContainsString('fill="currentColor"', $card);
        $this->assertStringContainsString('card-savings-note', $card);
        $this->assertStringContainsString('pricing-value-badge', $card);
        $this->assertStringContainsString('Popular verified stays in Lagos', $landing);
        $this->assertStringContainsString('x-ref="rail"', $landing);
        $this->assertStringContainsString('marketplace-row-controls flex', $landing);
        $this->assertStringContainsString('marketplace-row-nav', $landing);
        $this->assertStringContainsString('marketplace-row-header', $landing);
        $this->assertStringContainsString('marketplace-row-rail', $landing);
        $this->assertStringNotContainsString('hidden items-center gap-2 sm:flex', $landing);
        $this->assertStringContainsString('<x-marketplace-property-tile', $landing);
        $this->assertStringContainsString('AI Insights preview', $landing);
        $this->assertStringContainsString('Generated from verified marketplace data', $landing);
        $this->assertStringContainsString('qualityTitle', $tile);
        $this->assertStringContainsString('pricingTitle', $tile);

        $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/components.css');
        $this->assertStringContainsString('.marketplace-row-rail .card-listing', $css);
        $this->assertStringContainsString('.marketplace-row-rail .card-img-wrap', $css);
        $this->assertMatchesRegularExpression('/@media\s*\(max-width:\s*767px\).*?\.marketplace-row-rail\s*>\s*div\s*\{[^}]*width:\s*246px;[^}]*min-width:\s*246px;/s', $css);
    }

    public function test_marketplace_booking_confirmation_shows_a_complete_live_quote(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/marketplace/show.blade.php');

        $this->assertStringContainsString('get guestCount()', $view);
        $this->assertStringContainsString('get subtotal()', $view);
        $this->assertStringContainsString('get discount()', $view);
        $this->assertStringContainsString('get total(){return this.serverQuote?Number(this.serverQuote.total):this.subtotal-this.discount}', $view);
        $this->assertStringContainsString('Total amount', $view);
        $this->assertStringContainsString('guestCount>capacity', $view);
        $this->assertStringContainsString("'Confirm & book · '+formatMoney(total)", $view);
        $this->assertStringNotContainsString('Service fee (5%)', $view);
        $this->assertStringNotContainsString('Eligible stay discount', $view);
    }

    public function test_marketplace_booking_form_shows_live_total_and_keeps_check_in_when_checkout_changes(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/marketplace/show.blade.php');

        $this->assertStringContainsString('Estimated total', $view);
        $this->assertStringContainsString('Select check-in and checkout to see the total', $view);
        $this->assertStringContainsString('No separate service fee is added.', $view);
        $this->assertStringContainsString('Best value · save', $view);
        $this->assertStringContainsString('get nextDiscountHint()', $view);
        $this->assertStringContainsString('Hosted by {{ $hostDisplayName }}', $view);
        $this->assertStringContainsString('primary_contact_name', $view);
        $this->assertStringContainsString('sm:grid-cols-2', $view);
        $this->assertStringNotContainsString('min-w-[620px]', $view);
        $this->assertStringContainsString("if(!this.checkIn||day.date<=this.checkIn){this.checkIn=day.date;this.checkOut='';return}", $view);
        $this->assertStringNotContainsString('if(!this.checkIn||this.checkOut||day.date<=this.checkIn)', $view);
    }

    public function test_landing_search_bar_is_a_live_filter_control(): void
    {
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/components/search-bar.blade.php');

        $this->assertStringContainsString('x-data="searchBar(@js($filters))"', $view);
        $this->assertStringNotContainsString('method="GET"'."\n".'            x-data=', $view);
        $this->assertStringContainsString('name="q"', $view);
        $this->assertStringContainsString('search-mobile-toggle', $view);
        $this->assertStringContainsString('mobileExpanded', $view);
        $this->assertStringContainsString('get mobileSummary()', $view);
        $this->assertStringContainsString('x-model="where"', $view);
        $this->assertStringContainsString('name="check_in"', $view);
        $this->assertStringContainsString('name="check_out"', $view);
        $this->assertStringContainsString('x-model="checkIn"', $view);
        $this->assertStringContainsString('x-model="checkOut"', $view);
        $this->assertStringContainsString('get datesDisplay()', $view);
        $this->assertStringContainsString('x-text="datesDisplay || \'Add dates\'"', $view);
        $this->assertStringContainsString('incrementGuests()', $view);
        $this->assertStringContainsString('decrementGuests()', $view);
        $this->assertStringContainsString("route('home').'#marketplace'", $view);

        $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/components.css');

        $this->assertStringContainsString('.search-mobile-toggle', $css);
        $this->assertMatchesRegularExpression('/@media\s*\(max-width:\s*767px\).*?\.search-bar\s*\{[^}]*display:\s*none;.*?\.search-bar\.is-expanded\s*\{[^}]*display:\s*flex;/s', $css);
    }

    public function test_marketplace_advanced_filters_are_collapsible_on_mobile(): void
    {
        $root = dirname(__DIR__, 2);
        $landing = file_get_contents($root.'/resources/views/welcome.blade.php');
        $fallback = file_get_contents($root.'/resources/views/index2.blade.php');
        $css = file_get_contents($root.'/resources/css/components.css');

        foreach ([$landing, $fallback] as $view) {
            $this->assertStringContainsString('marketplace-advanced-filters', $view);
            $this->assertStringContainsString('marketplace-advanced-summary', $view);
            $this->assertStringContainsString('More filters', $view);
            $this->assertStringContainsString('marketplace-advanced-form', $view);
        }

        $this->assertStringContainsString('.marketplace-advanced-summary', $css);
        $this->assertStringContainsString('.marketplace-advanced-filters:not([open]) > .marketplace-advanced-form', $css);
        $this->assertMatchesRegularExpression('/@media\s*\(max-width:\s*767px\).*?\.marketplace-advanced-summary\s*\{[^}]*display:\s*flex;/s', $css);
    }

    public function test_public_layout_exposes_csrf_token_for_marketplace_ajax_requests(): void
    {
        $layout = file_get_contents(dirname(__DIR__, 2).'/resources/views/layouts/app.blade.php');

        $this->assertStringContainsString('<meta name="csrf-token" content="{{ csrf_token() }}">', $layout);
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
