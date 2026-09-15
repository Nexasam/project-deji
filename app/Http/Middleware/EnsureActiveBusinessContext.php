<?php

namespace App\Http\Middleware;

use App\Support\ActiveBusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBusinessContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $record = $request->user()?->resolvedBusinessContext();
        if (! $record || ! $record->business || ! $record->membership || ! $record->activeRoleAssignment) {
            return redirect()->route('owner.onboarding.business.create');
        }
        if ($record->membership->status->value !== 'active' || $record->business->status->value === 'inactive') {
            abort(403);
        }

        $context = new ActiveBusinessContext($record->business, $record->membership, $record->activeRoleAssignment);
        app()->instance(ActiveBusinessContext::class, $context);
        $request->attributes->set('businessContext', $context);
        view()->share('activeBusiness', $context->business);
        view()->share('activeBusinessContext', $context);
        view()->share('availableBusinessMemberships', $request->user()->businessMemberships()
            ->with(['business', 'roles.role'])->where('status', 'active')->orderBy('created_at')->get());

        return $next($request);
    }
}
