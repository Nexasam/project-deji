<?php

namespace App\Http\Middleware;

use App\Services\PlatformPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureGuestSurfaceAccess
{
    public function __construct(private readonly PlatformPermissionService $platformPermissions)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->canAccessGuestWorkspace()) {
            if ($this->platformPermissions->isPlatformAdministrator($user)) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->businessMemberships()->where('status', 'active')->exists()) {
                return redirect()->route('owner.entry');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
