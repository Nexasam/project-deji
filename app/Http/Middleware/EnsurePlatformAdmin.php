<?php

namespace App\Http\Middleware;

use App\Services\PlatformPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function __construct(private readonly PlatformPermissionService $permissions) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->permissions->isPlatformAdministrator($request->user()), 403);

        return $next($request);
    }
}
