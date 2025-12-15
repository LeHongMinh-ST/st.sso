<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Services;

use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;

/**
 * Token Generator Interface.
 * Defines contract for generating and managing OAuth2 tokens.
 *
 * SECURITY: This interface ensures proper token handling:
 * - Token expiration
 * - Token revocation
 * - Scope validation
 * - Secure token generation
 */
interface TokenGeneratorInterface
{
    /**
     * Generate access token for user and client.
     *
     * SECURITY: Token should have proper expiration (typically 1 hour).
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @param Client $client Client aggregate
     * @param array<string> $scopes Token scopes
     * @return string Access token (JWT or opaque token)
     */
    public function generateAccessToken(UserIdentity $userIdentity, Client $client, array $scopes = []): string;

    /**
     * Generate refresh token.
     *
     * SECURITY: Refresh token should have longer expiration (typically 30 days).
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @param Client $client Client aggregate
     * @return string Refresh token
     */
    public function generateRefreshToken(UserIdentity $userIdentity, Client $client): string;

    /**
     * Validate token and return user information.
     *
     * SECURITY: Validates token signature, expiration, and scope.
     *
     * @param string $token Access token to validate
     * @return array<string, mixed>|null User information if token is valid, null otherwise
     *         Returns array with keys: user_identity_id, client_id, scopes, etc.
     */
    public function validateToken(string $token): ?array;

    /**
     * Revoke token.
     *
     * SECURITY: Marks token as revoked, preventing further use.
     *
     * @param string $token Token to revoke
     * @return void
     */
    public function revokeToken(string $token): void;

    /**
     * Revoke all tokens for a user identity.
     *
     * SECURITY: Useful for logout or security incidents.
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @return void
     */
    public function revokeAllTokensForUser(UserIdentity $userIdentity): void;
}
