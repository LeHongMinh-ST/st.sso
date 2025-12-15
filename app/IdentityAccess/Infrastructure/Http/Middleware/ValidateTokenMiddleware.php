<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Http\Middleware;

use App\IdentityAccess\Application\UseCases\ValidateTokenUseCase;
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
     */
    public function __construct(
        private readonly ValidateTokenUseCase $validateTokenUseCase,
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
                // Get User model from UserIdentity ID
                $user = $this->getUserFromUserIdentityId($userInfo['user_identity_id']);
                if (null !== $user) {
                    $request->setUserResolver(fn () => $user);
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

    /**
     * Get User model from UserIdentity ID.
     * Bridge method to maintain compatibility with Laravel Auth.
     *
     * @param string $userIdentityId User Identity ID (UUID)
     * @return \App\OrganizationalStructure\Infrastructure\Eloquent\User|null
     */
    private function getUserFromUserIdentityId(string $userIdentityId): ?\App\OrganizationalStructure\Infrastructure\Eloquent\User
    {
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn) {
            return \App\OrganizationalStructure\Infrastructure\Eloquent\User::where('uuid', $userIdentityId)->first();
        }

        // Fallback: try to find by deterministic UUID
        $records = \Illuminate\Support\Facades\DB::table('users')->select('id')->get();
        foreach ($records as $record) {
            $generatedUuid = $this->generateDeterministicUuid('users', $record->id);
            if ($generatedUuid === $userIdentityId) {
                return \App\OrganizationalStructure\Infrastructure\Eloquent\User::find($record->id);
            }
        }

        return null;
    }

    /**
     * Generate deterministic UUID from integer ID.
     * Temporary helper until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }
}
