<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\IdentityAccess\Application\DTOs\AuthenticateUserDTO;
use App\IdentityAccess\Application\UseCases\AuthenticateUserUseCase;
use App\IdentityAccess\Application\UseCases\AuthenticateWithMicrosoftUseCase;
use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
use App\IdentityAccess\Domain\Exceptions\UserIdentityNotFoundException;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Authentication controller.
 *
 * SECURITY: Uses Use Cases for authentication logic, ensuring proper security measures.
 */
final class AuthenticateController extends Controller
{
    /**
     * @param AuthenticateUserUseCase $authenticateUserUseCase
     * @param AuthenticateWithMicrosoftUseCase $authenticateWithMicrosoftUseCase
     */
    public function __construct(
        private readonly AuthenticateUserUseCase $authenticateUserUseCase,
        private readonly AuthenticateWithMicrosoftUseCase $authenticateWithMicrosoftUseCase,
    ) {
    }

    /**
     * Show login form.
     *
     * @return View|Application|Factory|RedirectResponse
     */
    public function showLoginForm(): View|Application|Factory|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.auth.login');
    }

    /**
     * Handle login request.
     *
     * SECURITY: Uses AuthenticateUserUseCase which implements rate limiting and generic error messages.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $dto = new AuthenticateUserDTO(
                username: $request->input('username'),
                password: $request->input('password'),
            );

            $userIdentity = $this->authenticateUserUseCase->execute($dto);

            // Get User model for Laravel Auth
            $user = $this->getUserModelFromUserIdentity($userIdentity);

            if (null === $user) {
                return redirect()->back()
                    ->withErrors(['message' => ['Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!']])
                    ->withInput();
            }

            // Check if user is only login with Microsoft
            if ($userIdentity->isOnlyMicrosoftLogin()) {
                return redirect()->back()
                    ->withErrors(['message' => ['Tài khoản chỉ được đăng nhập từ Microsoft!']])
                    ->withInput();
            }

            // Login user
            Auth::login($user, (bool) $request->get('remember'));

            return redirect()->intended(route('dashboard'));
        } catch (InvalidCredentialsException $e) {
            // Generic error message to prevent user enumeration
            return redirect()->back()
                ->withErrors(['message' => ['Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!']])
                ->withInput();
        } catch (Throwable $e) {
            // Log error for debugging (but don't expose to user)
            \Illuminate\Support\Facades\Log::error('Authentication error', [
                'username' => $request->input('username'),
                'exception' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['message' => ['Đã xảy ra lỗi. Vui lòng thử lại sau!']])
                ->withInput();
        }
    }

    /**
     * Handle logout request.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login');
    }

    /**
     * Redirect to Microsoft OAuth provider.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectToSocialite(Request $request): RedirectResponse
    {
        $url = Socialite::driver('azure')
            ->with(['prompt' => 'select_account'])
            ->stateless()->redirect()->getTargetUrl();

        return redirect($url);
    }

    /**
     * Handle Microsoft OAuth callback.
     *
     * SECURITY: Uses AuthenticateWithMicrosoftUseCase for secure Microsoft authentication.
     *
     * @return RedirectResponse
     */
    public function handleSocialteCallback(): RedirectResponse
    {
        try {
            $azureUser = Socialite::driver('azure')->stateless()->user();
            $email = $azureUser->getEmail();

            // Use AuthenticateWithMicrosoftUseCase
            $userIdentity = $this->authenticateWithMicrosoftUseCase->execute($email);

            // Get User model for Laravel Auth
            $user = $this->getUserModelFromUserIdentity($userIdentity);

            if (null === $user) {
                return redirect()->route('login')
                    ->withErrors(['message' => ['Không tìm thấy tài khoản!']]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (UserIdentityNotFoundException $e) {
            // User not found - this might be a new user from Microsoft
            // Handle user creation (this should be in OrganizationalStructure context)
            // For now, return error
            return redirect()->route('login')
                ->withErrors(['message' => ['Tài khoản chưa được đăng ký trong hệ thống!']]);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Microsoft authentication error', [
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['message' => ['Đã xảy ra lỗi. Vui lòng thử lại sau!']]);
        }
    }

    /**
     * Get User model from UserIdentity.
     *
     * Maps UserIdentity (IdentityAccess context) to User model (OrganizationalStructure context).
     *
     * @param \App\IdentityAccess\Domain\Aggregates\UserIdentity $userIdentity
     * @return User|null
     */
    private function getUserModelFromUserIdentity(\App\IdentityAccess\Domain\Aggregates\UserIdentity $userIdentity): ?User
    {
        // UserIdentity and User share the same table (users)
        // Use UUID or integer ID to find the User model
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn) {
            return User::where('uuid', $userIdentity->id()->toString())->first();
        }

        // Fallback: try to find by email
        return User::where('email', $userIdentity->email()->toString())->first();
    }
}
