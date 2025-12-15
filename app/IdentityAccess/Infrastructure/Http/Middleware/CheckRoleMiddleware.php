<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Middleware;

use App\IdentityAccess\Application\Services\AuthorizationService;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to check user roles.
 * Uses AuthorizationService from IdentityAccess Context.
 *
 * SECURITY: Default deny - returns 403 if user doesn't have role.
 */
final class CheckRoleMiddleware
{
    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * SECURITY:
     * - Default deny (abort 403 if no role)
     * - Proper error responses (403, not 401)
     * - No information leakage
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role Role name to check
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (null === $user) {
            abort(401, 'Unauthenticated');
        }

        try {
            if (!$this->hasRole($user, $role)) {
                abort(403, 'Bạn không có quyền truy cập trang này.');
            }
        } catch (Throwable $e) {
            // Log error but don't expose details
            \Illuminate\Support\Facades\Log::error('Role check failed', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);

            // Default deny on error
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }

    /**
     * Check if user has role.
     * Bridge method to check role from Eloquent User model.
     *
     * @param EloquentUser $user Eloquent User model
     * @param string $roleName Role name to check
     * @return bool True if user has role, false otherwise
     */
    private function hasRole(EloquentUser $user, string $roleName): bool
    {
        $roles = $user->roles;

        foreach ($roles as $role) {
            if ($role->name === $roleName) {
                return true;
            }
        }

        return false;
    }
}
