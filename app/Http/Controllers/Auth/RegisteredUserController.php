<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterUserService;
use App\Support\IntendedUrl;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        IntendedUrl::rememberFrom($request);

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(RegisterUserRequest $request, RegisterUserService $registrar): RedirectResponse
    {
        $user = $registrar->register($request->validated());

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('verification.notice', absolute: false));
    }
}
