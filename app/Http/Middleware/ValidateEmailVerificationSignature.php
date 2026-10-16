<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response;

class ValidateEmailVerificationSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasValidRelativeSignature() && ! $request->hasValidSignature()) {
            throw new InvalidSignatureException;
        }

        return $next($request);
    }
}
