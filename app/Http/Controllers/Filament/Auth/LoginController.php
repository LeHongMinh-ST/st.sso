<?php

declare(strict_types=1);

namespace App\Http\Controllers\Filament\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authenticate\LoginRequest;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): View
    {
        return view('pages.auth.login');
    }

    /**
     * Handle the login request.
     */
    public function store(LoginRequest $request): LoginResponse|RedirectResponse
    {
        $request->merge([$this->username() => request()->input('username')]);
        $credentials = $request->only([$this->username(), 'password']);

        if (! Auth::attempt($credentials, (bool) ($request->get('remember')))) {
            return redirect()->back()
                ->withErrors(['message' => ['Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!']])
                ->withInput();
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->back()
                ->withErrors(['message' => ['Đăng nhập thất bại!']])
                ->withInput();
        }

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Auth::logout();

            return redirect()->back()
                ->withErrors(['message' => ['Bạn không có quyền truy cập vào hệ thống!']])
                ->withInput();
        }

        // Đặt session cho Filament
        session()->put('filament.sso.auth.id', $user->getAuthIdentifier());
        session()->put('filament.sso.auth.user', $user);
        session()->put('filament.sso.auth.remember', (bool) ($request->get('remember')));

        return app(LoginResponse::class);
    }

    private function username(): string
    {
        return filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
    }
}
