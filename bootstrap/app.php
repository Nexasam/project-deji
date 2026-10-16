<?php

use App\Http\Middleware\EnsureActiveBusinessContext;
use App\Http\Middleware\EnsureBusinessMutationAllowed;
use App\Http\Middleware\EnsureBusinessOwner;
use App\Http\Middleware\EnsureBusinessPermission;
use App\Http\Middleware\EnsureGuestSurfaceAccess;
use App\Http\Middleware\EnsureGuestWorkspaceAccess;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\EnsurePlatformOwner;
use App\Http\Middleware\EnsurePlatformPermission;
use App\Http\Middleware\ProductionSecurityHeaders;
use App\Http\Middleware\ValidateEmailVerificationSignature;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ProductionSecurityHeaders::class);
        $middleware->validateCsrfTokens(except: [
            'webhooks/paystack',
            'webhooks/flutterwave',
        ]);
        $middleware->alias([
            'business.context' => EnsureActiveBusinessContext::class,
            'business.owner' => EnsureBusinessOwner::class,
            'business.mutation' => EnsureBusinessMutationAllowed::class,
            'permission' => EnsureBusinessPermission::class,
            'guest.workspace' => EnsureGuestWorkspaceAccess::class,
            'guest.surface' => EnsureGuestSurfaceAccess::class,
            'platform.admin' => EnsurePlatformAdmin::class,
            'platform.owner' => EnsurePlatformOwner::class,
            'platform.permission' => EnsurePlatformPermission::class,
            'verification.signed' => ValidateEmailVerificationSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $verificationFailure = function (Request $request) {
            if (! $request->routeIs('verification.verify')) {
                return null;
            }

            return $request->user()
                ? redirect()->route('verification.notice')->with('status', 'verification-link-invalid')
                : redirect()->route('login')->with('verification_error', 'That verification link is no longer valid. Sign in and request a fresh one.');
        };

        $exceptions->render(
            fn (InvalidSignatureException $exception, Request $request) => $verificationFailure($request),
        );

        $exceptions->render(
            fn (AuthorizationException $exception, Request $request) => $verificationFailure($request),
        );
    })->create();
