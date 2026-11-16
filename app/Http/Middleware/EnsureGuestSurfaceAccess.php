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

        // Business and platform users may inspect a published listing from the
        // owner workspace, but they still cannot enter guest-only workflows.
        $readOnlyListingPreview = $request->routeIs('marketplace.show', 'marketplace.reviews');

        if ($user && ! $user->canAccessGuestWorkspace() && ! $readOnlyListingPreview) {
            return redirect()->route($this->destination->routeName($user));
        }

        return $next($request);
    }
}
