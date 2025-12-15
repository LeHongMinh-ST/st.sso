<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Persistence;

use App\IdentityAccess\Domain\Entities\AccessToken;
use App\IdentityAccess\Domain\Repositories\AccessTokenRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\SharedKernel\Domain\ValueObjects\Timestamp;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token as EloquentToken;

/**
 * Eloquent implementation of AccessTokenRepositoryInterface.
 *
 * Note: Uses Laravel Passport Token model.
 */
final class EloquentAccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * Find AccessToken by token ID.
     *
     * @param string $tokenId Token identifier
     * @return AccessToken|null
     */
    public function findByTokenId(string $tokenId): ?AccessToken
    {
        $eloquentToken = EloquentToken::find($tokenId);

        if (null === $eloquentToken) {
            return null;
        }

        return $this->toDomain($eloquentToken);
    }

    /**
     * Find AccessTokens by User Identity ID.
     *
     * @param UserIdentityId $userIdentityId User Identity ID
     * @return array<AccessToken>
     */
    public function findByUserIdentityId(UserIdentityId $userIdentityId): array
    {
        // Laravel Passport uses user_id (integer) in oauth_access_tokens table
        // We need to map UUID to integer ID
        $integerUserId = $this->getIntegerIdFromUuid('users', $userIdentityId->toString());

        if (null === $integerUserId) {
            return [];
        }

        $eloquentTokens = EloquentToken::where('user_id', $integerUserId)->get();

        return $eloquentTokens->map(fn ($token) => $this->toDomain($token))->toArray();
    }

    /**
     * Find AccessTokens by Client ID.
     *
     * @param ClientId $clientId Client ID
     * @return array<AccessToken>
     */
    public function findByClientId(ClientId $clientId): array
    {
        $eloquentTokens = EloquentToken::where('client_id', $clientId->toString())->get();

        return $eloquentTokens->map(fn ($token) => $this->toDomain($token))->toArray();
    }

    /**
     * Save AccessToken entity.
     *
     * @param AccessToken $accessToken Access Token entity
     * @return void
     */
    public function save(AccessToken $accessToken): void
    {
        // Note: Laravel Passport manages tokens, so we typically don't save directly
        // This method is here for interface compliance
        // In practice, tokens are created via Passport's token generation
    }

    /**
     * Delete AccessToken entity.
     *
     * @param string $tokenId Token identifier
     * @return void
     */
    public function delete(string $tokenId): void
    {
        EloquentToken::destroy($tokenId);
    }

    /**
     * Revoke all tokens for a user identity.
     *
     * @param UserIdentityId $userIdentityId User Identity ID
     * @return void
     */
    public function revokeAllForUser(UserIdentityId $userIdentityId): void
    {
        $integerUserId = $this->getIntegerIdFromUuid('users', $userIdentityId->toString());

        if (null !== $integerUserId) {
            EloquentToken::where('user_id', $integerUserId)->update(['revoked' => true]);
        }
    }

    /**
     * Revoke all tokens for a client.
     *
     * @param ClientId $clientId Client ID
     * @return void
     */
    public function revokeAllForClient(ClientId $clientId): void
    {
        EloquentToken::where('client_id', $clientId->toString())->update(['revoked' => true]);
    }

    /**
     * Map Eloquent model to Domain entity.
     *
     * @param EloquentToken $eloquentToken Eloquent token model
     * @return AccessToken Access Token entity
     */
    private function toDomain(EloquentToken $eloquentToken): AccessToken
    {
        $userIdentityId = UserIdentityId::fromString($this->getUuidFromIntegerId('users', $eloquentToken->user_id));
        $clientId = ClientId::fromString((string) $eloquentToken->client_id);
        $scopes = $eloquentToken->scopes ?? [];
        $expiresAt = Timestamp::fromDateTime(
            DateTimeImmutable::createFromMutable($eloquentToken->expires_at)
        );

        return AccessToken::fromPersistence(
            (string) $eloquentToken->id,
            $userIdentityId,
            $clientId,
            $scopes,
            $expiresAt,
            (bool) $eloquentToken->revoked,
        );
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
            $uuid = DB::table($table)->where('id', $integerId)->value('uuid');
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
            return DB::table($table)->where('uuid', $uuid)->value('id');
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
        $records = DB::table($table)->select('id')->get();

        foreach ($records as $record) {
            $generatedUuid = $this->generateDeterministicUuid($table, $record->id);
            if ($generatedUuid === $uuid) {
                return $record->id;
            }
        }

        return null;
    }
}
