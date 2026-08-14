<?php

namespace App\Http\Middleware;

use App\Support\ActiveBusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(ActiveBusinessContext::class);
        if (! $request->user()->hasActiveBusinessRole('business_owner', $context->membership->id)) {
            abort(403);
        }

        return $next($request);
    }
}
