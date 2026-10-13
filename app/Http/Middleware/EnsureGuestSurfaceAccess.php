<?php

namespace App\Http\Middleware;

use App\Support\UserWorkspaceDestination;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureGuestSurfaceAccess
{
    public function __construct(private readonly UserWorkspaceDestination $destination)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->canAccessGuestWorkspace()) {
            return redirect()->route($this->destination->routeName($user));
        }

        return $next($request);
    }
}
