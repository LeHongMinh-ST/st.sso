<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Entities;

use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * Access Token entity.
 * Represents an OAuth2 access token.
 *
 * Note: This is an Entity, not an Aggregate Root, because:
 * - Tokens are managed by Laravel Passport
 * - Tokens don't have complex business logic
 * - Tokens are typically short-lived and don't need event sourcing
 *
 * SECURITY: This entity never stores the actual token value in domain events.
 */
final class AccessToken
{
    private string $tokenId; // Token identifier (not the actual token)
    private UserIdentityId $userIdentityId;
    private ClientId $clientId;
    /** @var array<string> Token scopes */
    private array $scopes;
    private Timestamp $expiresAt;
    private bool $isRevoked;

    /**
     * Private constructor to enforce immutability.
     *
     * @param string $tokenId Token identifier
     * @param UserIdentityId $userIdentityId User Identity ID
     * @param ClientId $clientId Client ID
     * @param array<string> $scopes Token scopes
     * @param Timestamp $expiresAt Expiration timestamp
     * @param bool $isRevoked Whether token is revoked
     */
    private function __construct(
        string $tokenId,
        UserIdentityId $userIdentityId,
        ClientId $clientId,
        array $scopes,
        Timestamp $expiresAt,
        bool $isRevoked = false,
    ) {
        $this->tokenId = $tokenId;
        $this->userIdentityId = $userIdentityId;
        $this->clientId = $clientId;
        $this->scopes = $scopes;
        $this->expiresAt = $expiresAt;
        $this->isRevoked = $isRevoked;
    }

    /**
     * Create AccessToken from data.
     *
     * @param string $tokenId Token identifier
     * @param UserIdentityId $userIdentityId User Identity ID
     * @param ClientId $clientId Client ID
     * @param array<string> $scopes Token scopes
     * @param Timestamp $expiresAt Expiration timestamp
     * @return self
     */
    public static function create(
        string $tokenId,
        UserIdentityId $userIdentityId,
        ClientId $clientId,
        array $scopes,
        Timestamp $expiresAt,
    ): self {
        return new self(
            $tokenId,
            $userIdentityId,
            $clientId,
            $scopes,
            $expiresAt,
            false, // Not revoked when created
        );
    }

    /**
     * Reconstruct AccessToken from persistence.
     *
     * @param string $tokenId
     * @param UserIdentityId $userIdentityId
     * @param ClientId $clientId
     * @param array<string> $scopes
     * @param Timestamp $expiresAt
     * @param bool $isRevoked
     * @return self
     */
    public static function fromPersistence(
        string $tokenId,
        UserIdentityId $userIdentityId,
        ClientId $clientId,
        array $scopes,
        Timestamp $expiresAt,
        bool $isRevoked = false,
    ): self {
        return new self(
            $tokenId,
            $userIdentityId,
            $clientId,
            $scopes,
            $expiresAt,
            $isRevoked,
        );
    }

    /**
     * Revoke token.
     *
     * @return void
     */
    public function revoke(): void
    {
        $this->isRevoked = true;
    }

    /**
     * Check if token is expired.
     *
     * @param Timestamp $now Current timestamp
     * @return bool
     */
    public function isExpired(Timestamp $now): bool
    {
        return $this->expiresAt->isBefore($now) || $this->expiresAt->equals($now);
    }

    /**
     * Check if token is valid (not revoked and not expired).
     *
     * @param Timestamp $now Current timestamp
     * @return bool
     */
    public function isValid(Timestamp $now): bool
    {
        return !$this->isRevoked && !$this->isExpired($now);
    }

    /**
     * Check if token has scope.
     *
     * @param string $scope Scope to check
     * @return bool
     */
    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    /**
     * Get token ID.
     *
     * @return string
     */
    public function tokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * Get user identity ID.
     *
     * @return UserIdentityId
     */
    public function userIdentityId(): UserIdentityId
    {
        return $this->userIdentityId;
    }

    /**
     * Get client ID.
     *
     * @return ClientId
     */
    public function clientId(): ClientId
    {
        return $this->clientId;
    }

    /**
     * Get token scopes.
     *
     * @return array<string>
     */
    public function scopes(): array
    {
        return $this->scopes;
    }

    /**
     * Get expiration timestamp.
     *
     * @return Timestamp
     */
    public function expiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    /**
     * Check if token is revoked.
     *
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }
}
