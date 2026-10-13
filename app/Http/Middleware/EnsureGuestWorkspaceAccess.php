<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureGuestWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->canAccessGuestWorkspace(), 403);

        return $next($request);
    }
}
