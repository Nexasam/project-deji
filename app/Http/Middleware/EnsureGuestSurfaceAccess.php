<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureGuestSurfaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->canAccessGuestWorkspace()) {
            abort(403);
        }

        return $next($request);
    }
}
