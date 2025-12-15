<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Middleware;

use App\IdentityAccess\Application\UseCases\ValidateTokenUseCase;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Infrastructure\Services\UserIdentityBridgeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to validate access tokens using IdentityAccess Use Cases.
 *
 * SECURITY: Validates token signature, expiration, and scope.
 * Uses ValidateTokenUseCase from IdentityAccess Context.
 */
final class ValidateTokenMiddleware
{
    /**
     * @param ValidateTokenUseCase $validateTokenUseCase
     * @param UserIdentityBridgeService $bridgeService
     * @param UserIdentityRepositoryInterface $userIdentityRepository
     */
    public function __construct(
        private readonly ValidateTokenUseCase $validateTokenUseCase,
        private readonly UserIdentityBridgeService $bridgeService,
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * SECURITY:
     * - Validates token properly
     * - Generic error messages (không leak info)
     * - No sensitive data in responses
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (null === $token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Token not provided',
            ], 401);
        }

        try {
            $userInfo = $this->validateTokenUseCase->execute($token);

            if (null === $userInfo) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Invalid token',
                ], 401);
            }

            // Attach user info to request for use in controllers
            $request->merge(['user_info' => $userInfo]);

            // Also set user for Laravel Auth compatibility
            // Note: This is a bridge to maintain compatibility with existing code
            if (isset($userInfo['user_identity_id'])) {
                // Get UserIdentity ID
                $userIdentityId = UserIdentityId::fromString($userInfo['user_identity_id']);

                // Find UserIdentity using repository
                $userIdentity = $this->userIdentityRepository->findById($userIdentityId);

                if (null !== $userIdentity) {
                    // Get User model from UserIdentity using bridge service
                    $user = $this->bridgeService->getEloquentUser($userIdentity);
                    if (null !== $user) {
                        $request->setUserResolver(fn () => $user);
                    }
                }
            }

            return $next($request);
        } catch (Throwable $e) {
            // Log error but don't expose details to client
            \Illuminate\Support\Facades\Log::error('Token validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid token',
            ], 401);
        }
    }

}
