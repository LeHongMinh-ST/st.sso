<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Domain\Services\TokenGeneratorInterface;

/**
 * Use case for validating an access token.
 *
 * SECURITY: Validates token signature, expiration, and scope.
 */
final class ValidateTokenUseCase
{
    /**
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator,
    ) {
    }

    /**
     * Execute validate token use case.
     *
     * SECURITY: Validates token signature, expiration, and scope.
     *
     * @param string $token Access token to validate
     * @return array<string, mixed>|null User information if token is valid, null otherwise
     *         Returns array with keys: user_identity_id, client_id, scopes, etc.
     */
    public function execute(string $token): ?array
    {
        return $this->tokenGenerator->validateToken($token);
    }
}
