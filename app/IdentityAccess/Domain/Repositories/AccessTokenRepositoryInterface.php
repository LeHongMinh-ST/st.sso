<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repositories;

use App\IdentityAccess\Domain\Entities\AccessToken;
use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;

/**
 * Access Token Repository Interface.
 * Defines contract for persisting and retrieving AccessToken entities.
 *
 * Note: AccessToken is an Entity, not an Aggregate Root.
 */
interface AccessTokenRepositoryInterface
{
    /**
     * Find AccessToken by token ID.
     *
     * @param string $tokenId Token identifier
     * @return AccessToken|null
     */
    public function findByTokenId(string $tokenId): ?AccessToken;

    /**
     * Find AccessTokens by User Identity ID.
     *
     * @param UserIdentityId $userIdentityId User Identity ID
     * @return array<AccessToken>
     */
    public function findByUserIdentityId(UserIdentityId $userIdentityId): array;

    /**
     * Find AccessTokens by Client ID.
     *
     * @param ClientId $clientId Client ID
     * @return array<AccessToken>
     */
    public function findByClientId(ClientId $clientId): array;

    /**
     * Save AccessToken entity.
     *
     * @param AccessToken $accessToken Access Token entity
     * @return void
     */
    public function save(AccessToken $accessToken): void;

    /**
     * Delete AccessToken entity.
     *
     * @param string $tokenId Token identifier
     * @return void
     */
    public function delete(string $tokenId): void;

    /**
     * Revoke all tokens for a user identity.
     *
     * @param UserIdentityId $userIdentityId User Identity ID
     * @return void
     */
    public function revokeAllForUser(UserIdentityId $userIdentityId): void;

    /**
     * Revoke all tokens for a client.
     *
     * @param ClientId $clientId Client ID
     * @return void
     */
    public function revokeAllForClient(ClientId $clientId): void;
}
