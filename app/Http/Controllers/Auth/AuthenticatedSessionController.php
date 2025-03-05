<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Helper;
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
        $redirectAfterLogin = $request->get('redirect', null);

        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'redirect' => $redirectAfterLogin,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        $request->authenticate();

        $request->session()->regenerate();

        $redirectAfterLogin = $request->get('redirect', null);
        if ($redirectAfterLogin) {
            return redirect()->to($redirectAfterLogin);
        }

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

    public function redirectToSocialite(Request $request)
    {
        session(['redirect_after_login' => $request->query('redirect', route('dashboard', absolute: false))]);

        $url = Socialite::driver('azure')->stateless()->redirect()->getTargetUrl();

        return Inertia::location($url);
    }

    public function handleSocialteCallback()
    {
        $azureUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('email', $azureUser->getEmail())->first();

        if (! $user) {
            [$lastName, $firstName] = Helper::splitFullName($azureUser->getName());
            $user = User::create([
                'name' => $azureUser->getEmail(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $azureUser->getEmail(),
                'password' => Hash::make('password'),
            ]);
        }

        Auth::login($user, true);

        $redirectUrl = session('redirect_after_login', route('dashboard', absolute: false));

        session()->forget('redirect_after_login');

        if ($redirectUrl) {
            return Inertia::location($redirectUrl);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
