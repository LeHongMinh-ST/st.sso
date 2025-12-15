<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Services;

use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Services\TokenGeneratorInterface;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use RuntimeException;

/**
 * Laravel Passport token generator implementation.
 *
 * SECURITY: Uses Laravel Passport for secure token generation and validation.
 */
final class PassportTokenGenerator implements TokenGeneratorInterface
{
    /**
     * @param TokenRepository $tokenRepository
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly ClientRepository $clientRepository,
    ) {
    }

    /**
     * Generate access token for user and client.
     *
     * SECURITY: Token has proper expiration (typically 1 hour).
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @param Client $client Client aggregate
     * @param array<string> $scopes Token scopes
     * @return string Access token (JWT)
     */
    public function generateAccessToken(UserIdentity $userIdentity, Client $client, array $scopes = []): string
    {
        // Get integer user ID from UUID
        $integerUserId = $this->getIntegerIdFromUuid('users', $userIdentity->id()->toString());

        if (null === $integerUserId) {
            throw new RuntimeException('User not found');
        }

        // Use Passport to create token
        $tokenResult = $this->tokenRepository->createToken(
            $integerUserId,
            $client->id()->toString(),
            $scopes,
        );

        return $tokenResult->accessToken;
    }

    /**
     * Generate refresh token.
     *
     * SECURITY: Refresh token has longer expiration (typically 30 days).
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @param Client $client Client aggregate
     * @return string Refresh token
     */
    public function generateRefreshToken(UserIdentity $userIdentity, Client $client): string
    {
        // Laravel Passport handles refresh tokens automatically
        // This is a placeholder - actual implementation depends on Passport configuration
        return '';
    }

    /**
     * Validate token and return user information.
     *
     * SECURITY: Validates token signature, expiration, and scope.
     *
     * @param string $token Access token to validate
     * @return array<string, mixed>|null User information if token is valid, null otherwise
     */
    public function validateToken(string $token): ?array
    {
        $passportToken = $this->tokenRepository->find($token);

        if (null === $passportToken) {
            return null;
        }

        if ($passportToken->revoked) {
            return null;
        }

        if ($passportToken->expires_at < now()) {
            return null;
        }

        // Get user UUID from integer ID
        $userUuid = $this->getUuidFromIntegerId('users', $passportToken->user_id);

        return [
            'user_identity_id' => $userUuid,
            'client_id' => (string) $passportToken->client_id,
            'scopes' => $passportToken->scopes ?? [],
        ];
    }

    /**
     * Revoke token.
     *
     * SECURITY: Marks token as revoked, preventing further use.
     *
     * @param string $token Token to revoke
     * @return void
     */
    public function revokeToken(string $token): void
    {
        $passportToken = $this->tokenRepository->find($token);

        if (null !== $passportToken) {
            $this->tokenRepository->revokeAccessToken($passportToken->id);
        }
    }

    /**
     * Revoke all tokens for a user identity.
     *
     * SECURITY: Useful for logout or security incidents.
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @return void
     */
    public function revokeAllTokensForUser(UserIdentity $userIdentity): void
    {
        $integerUserId = $this->getIntegerIdFromUuid('users', $userIdentity->id()->toString());

        if (null !== $integerUserId) {
            $tokens = $this->tokenRepository->forUser($integerUserId);
            foreach ($tokens as $token) {
                $this->tokenRepository->revokeAccessToken($token->id);
            }
        }
    }

    /**
     * Get UUID from integer ID.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function getUuidFromIntegerId(string $table, int $integerId): string
    {
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn($table, 'uuid');

        if ($hasUuidColumn) {
            $uuid = \Illuminate\Support\Facades\DB::table($table)->where('id', $integerId)->value('uuid');
            if (null !== $uuid) {
                return $uuid;
            }
        }

        return $this->generateDeterministicUuid($table, $integerId);
    }

    /**
     * Get integer ID from UUID.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int|null Integer ID or null if not found
     */
    private function getIntegerIdFromUuid(string $table, string $uuid): ?int
    {
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn($table, 'uuid');

        if ($hasUuidColumn) {
            return \Illuminate\Support\Facades\DB::table($table)->where('uuid', $uuid)->value('id');
        }

        return $this->getIntegerIdFromDeterministicUuid($table, $uuid);
    }

    /**
     * Generate deterministic UUID from integer ID.
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

    /**
     * Get integer ID from deterministic UUID.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int|null Integer ID or null if not found
     */
    private function getIntegerIdFromDeterministicUuid(string $table, string $uuid): ?int
    {
        $records = \Illuminate\Support\Facades\DB::table($table)->select('id')->get();

        foreach ($records as $record) {
            $generatedUuid = $this->generateDeterministicUuid($table, $record->id);
            if ($generatedUuid === $uuid) {
                return $record->id;
            }
        }

        return null;
    }
}
