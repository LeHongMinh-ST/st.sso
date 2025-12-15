<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repositories;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\ValueObjects\Email;

/**
 * User Identity Repository Interface.
 * Defines contract for persisting and retrieving UserIdentity aggregates.
 */
interface UserIdentityRepositoryInterface
{
    /**
     * Generate a new UserIdentity ID.
     *
     * @return UserIdentityId
     */
    public function nextIdentity(): UserIdentityId;

    /**
     * Find UserIdentity by ID.
     *
     * @param UserIdentityId $id User Identity ID
     * @return UserIdentity|null
     */
    public function findById(UserIdentityId $id): ?UserIdentity;

    /**
     * Find UserIdentity by username.
     *
     * @param Username $username Username
     * @return UserIdentity|null
     */
    public function findByUsername(Username $username): ?UserIdentity;

    /**
     * Find UserIdentity by email.
     *
     * @param Email $email Email address
     * @return UserIdentity|null
     */
    public function findByEmail(Email $email): ?UserIdentity;

    /**
     * Find UserIdentity by OrganizationalStructure User ID.
     *
     * @param string $organizationalStructureUserId User ID from OrganizationalStructure context
     * @return UserIdentity|null
     */
    public function findByOrganizationalStructureUserId(string $organizationalStructureUserId): ?UserIdentity;

    /**
     * Save UserIdentity aggregate.
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @return void
     */
    public function save(UserIdentity $userIdentity): void;

    /**
     * Delete UserIdentity aggregate.
     *
     * @param UserIdentityId $id User Identity ID
     * @return void
     */
    public function delete(UserIdentityId $id): void;
}
