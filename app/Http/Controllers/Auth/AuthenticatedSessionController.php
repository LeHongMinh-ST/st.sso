<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToSocialite()
    {
        $url = Socialite::driver('azure')->stateless()->redirect()->getTargetUrl();

        return Inertia::location($url);
    }

    public function handleSocialteCallback()
    {
        $azureUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('email', $azureUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $azureUser->getName(),
                'email' => $azureUser->getEmail(),
                'password' => Hash::make('password'),
            ]);
        }

        Auth::login($user, true);

        return redirect('/dashboard');
    }
}
