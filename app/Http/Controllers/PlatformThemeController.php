<?php

namespace App\Http\Controllers;

use App\Services\Platform\PlatformSettings;
use Illuminate\Http\Response;

final class PlatformThemeController extends Controller
{
    public function __invoke(PlatformSettings $settings): Response
    {
        $font = match ($settings->get('appearance.font_family', 'system')) {
            'manrope' => "'Manrope',ui-sans-serif,system-ui,sans-serif",
            'inter' => "'Inter',ui-sans-serif,system-ui,sans-serif",
            default => "ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif",
        };
        $size = match ($settings->get('appearance.base_font_size', 'comfortable')) {
            'compact' => '15px', 'large' => '17px', default => '16px',
        };
        $accent = (string) $settings->get('appearance.accent_color', '#ea580c');
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $accent)) {
            $accent = '#ea580c';
        }

        $css = ":root{--vs-font:{$font};--vs-base-size:{$size};--vs-accent:{$accent}}html{font-size:var(--vs-base-size)}body,.font-sans,.public-surface{font-family:var(--vs-font)!important}.bg-orange-600{background-color:var(--vs-accent)!important}.text-orange-600,.text-orange-700{color:var(--vs-accent)!important}.border-orange-600{border-color:var(--vs-accent)!important}";

        return response($css, 200, ['Content-Type' => 'text/css; charset=UTF-8', 'Cache-Control' => 'public, max-age=300']);
    }
}
