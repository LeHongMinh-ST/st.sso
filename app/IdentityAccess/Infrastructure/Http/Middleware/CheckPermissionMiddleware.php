<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Middleware;

use App\IdentityAccess\Application\Services\AuthorizationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to check user permissions.
 * Uses AuthorizationService from IdentityAccess Context.
 *
 * SECURITY: Default deny - returns 403 if user doesn't have permission.
 */
final class CheckPermissionMiddleware
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
     * - Default deny (abort 403 if no permission)
     * - Proper error responses (403, not 401)
     * - No information leakage
     *
     * @param Request $request
     * @param Closure $next
     * @param string $permission Permission code to check
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (null === $user) {
            abort(401, 'Unauthenticated');
        }

        try {
            if (!$this->authorizationService->can($user, $permission)) {
                abort(403, 'Bạn không có quyền truy cập trang này.');
            }
        } catch (Throwable $e) {
            // Log error but don't expose details
            \Illuminate\Support\Facades\Log::error('Permission check failed', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage(),
            ]);

            // Default deny on error
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
