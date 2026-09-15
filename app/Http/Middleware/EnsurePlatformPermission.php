<?php

namespace App\Http\Middleware;

use App\Services\PlatformPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformPermission
{
    public function __construct(private readonly PlatformPermissionService $permissions) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        abort_unless($this->permissions->allows($request->user(), $permission), 403);

        return $next($request);
    }
}
