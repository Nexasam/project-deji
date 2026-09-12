<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OwnerWorkspaceLayoutTest extends TestCase
{
    #[DataProvider('wideWorkspaceViews')]
    public function test_owner_workspace_pages_use_the_wide_properties_container(string $view): void
    {
        $contents = file_get_contents(resource_path("views/{$view}.blade.php"));

        $this->assertStringContainsString('max-w-[1600px]', $contents);
        $this->assertStringNotContainsString('max-w-[1200px]', $contents);
        $this->assertStringNotContainsString('max-w-7xl', $contents);
    }

    public static function wideWorkspaceViews(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'bookings' => ['owner/bookings'],
            'finance' => ['owner/finance'],
        ];
    }

    public function test_properties_content_heading_matches_the_dashboard_scale(): void
    {
        $contents = file_get_contents(resource_path('views/owner/properties/index.blade.php'));

        $this->assertStringContainsString('text-xl font-extrabold tracking-tight">Property portfolio', $contents);
        $this->assertStringNotContainsString('text-3xl font-extrabold tracking-tight">Property portfolio', $contents);
    }
}
