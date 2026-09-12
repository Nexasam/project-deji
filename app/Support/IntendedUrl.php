<?php

namespace App\Support;

use Illuminate\Http\Request;

final class IntendedUrl
{
    public static function rememberFrom(Request $request): void
    {
        $redirect = $request->query('redirect');

        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            $request->session()->put('url.intended', $redirect);
        }
    }
}
