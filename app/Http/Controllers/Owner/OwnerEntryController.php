<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OwnerEntryController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return auth()->user()->resolvedBusinessContext()
            ? redirect()->route('owner.dashboard')
            : redirect()->route('owner.onboarding.business.create');
    }
}
