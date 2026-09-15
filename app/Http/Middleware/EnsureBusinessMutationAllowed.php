<?php

namespace App\Http\Middleware;

use App\Support\ActiveBusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessMutationAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(ActiveBusinessContext::class);

        abort_if($context->business->status->value !== 'active', 403, 'This business is suspended. New changes are temporarily disabled.');

        return $next($request);
    }
}
