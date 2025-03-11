<?php

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
use Laravel\Passport\Client;
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

        if ($request->has('client_id')) {
            $client = Client::where('id', $request->get('client_id'))->first();
            $redirect = $client->redirect_uri;
            $user = Auth::user();
            $query = http_build_query([
                'client_id'     => $client->id,
                'redirect_uri'  => $redirect,
                'response_type' => 'code',
                'scope'         => '',
                'state'         => $user->id,
            ]);
            $ssoServerAuth = route('passport.authorizations.authorize');
            return redirect("$ssoServerAuth?$query");
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login');
    }

    private function username(): string
    {
        return filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
    }

    public function redirectToSocialite(Request $request): RedirectResponse
    {
        $redirectAfterLogin = $request->get('redirect', null);

        session(['redirect_after_login' => $redirectAfterLogin]);

        $url = Socialite::driver('azure')->stateless()->redirect()->getTargetUrl();

        return redirect($url);
    }

    public function handleSocialteCallback(): RedirectResponse
    {
        $azureUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('email', $azureUser->getEmail())->first();

        if (! $user) {
            $name = Helper::splitFullName($azureUser->getName());
            $user = User::create([
                'user_name' => $azureUser->getEmail(),
                'last_name' => $name['last_name'],
                'first_name' => $name['first_name'],
                'email' => $azureUser->getEmail(),
                'password' => 'password',
                'role' => Role::Officer->value,
                'status' => 'active',
                'code' => 'ST-OFFICER-'.time(),
            ]);
        }

        Auth::login($user, true);

        $redirectUrl = session('redirect_after_login');

        session()->forget('redirect_after_login');

        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
