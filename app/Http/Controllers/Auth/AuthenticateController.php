<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authenticate\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticateController extends Controller
{
    public function showLoginForm(): View|Application|Factory|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->merge([$this->username() => request()->input('username')]);
        $credentials = $request->only([$this->username(), 'password']);
        if (! Auth::attempt($credentials, (bool) ($request->get('remember')))) {
            return redirect()->back()
                ->withErrors(['message' => ['Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!']])
                ->withInput();
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function redirectToSocialite(Request $request): RedirectResponse
    {
        $url = Socialite::driver('azure')
            ->with(['prompt' => 'select_account'])
            ->stateless()->redirect()->getTargetUrl();

        return redirect($url);
    }

    public function handleSocialteCallback(): RedirectResponse
    {
        $azureUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('email', $azureUser->getEmail())->first();
        if (! $user) {
            $name = Helper::splitFullName($azureUser->getName());

            // Set role and code based on jobTitle
            $role = Role::Officer->value;
            $code = 'ST-OFFICER-' . time();
            $emailParts = explode('@', $azureUser->getEmail());

            // If user is a student (jobTitle is "Sinh viên")
            if (isset($emailParts[1]) && 'sv.vnua.edu.vn' === $emailParts[1]) {
                return redirect()->route('login')->withErrors(['message' => ['Tài khoản sinh viên chưa tồn tại']]);
            }

            $user = User::create([
                'user_name' => $azureUser->getEmail(),
                'last_name' => $name['last_name'],
                'first_name' => $name['first_name'],
                'email' => $azureUser->getEmail(),
                'password' => 'password',
                'role' => $role,
                'status' => 'active',
                'code' => $code,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function username(): string
    {
        return filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
    }
}
