<?php

use App\Http\Middleware\EnsureActiveBusinessContext;
use App\Http\Middleware\EnsureBusinessMutationAllowed;
use App\Http\Middleware\EnsureBusinessOwner;
use App\Http\Middleware\EnsureBusinessPermission;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\EnsurePlatformPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'business.context' => EnsureActiveBusinessContext::class,
            'business.owner' => EnsureBusinessOwner::class,
            'business.mutation' => EnsureBusinessMutationAllowed::class,
            'permission' => EnsureBusinessPermission::class,
            'platform.admin' => EnsurePlatformAdmin::class,
            'platform.permission' => EnsurePlatformPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
