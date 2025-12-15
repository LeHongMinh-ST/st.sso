<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repositories;

use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\ValueObjects\RoleId;

/**
 * Role Repository Interface.
 * Defines contract for persisting and retrieving Role aggregates.
 */
interface RoleRepositoryInterface
{
    /**
     * Generate a new Role ID.
     *
     * @return RoleId
     */
    public function nextIdentity(): RoleId;

    /**
     * Find Role by ID.
     *
     * @param RoleId $id Role ID
     * @return Role|null
     */
    public function findById(RoleId $id): ?Role;

    /**
     * Find Role by name.
     *
     * @param string $name Role name
     * @return Role|null
     */
    public function findByName(string $name): ?Role;

    /**
     * Find all roles.
     *
     * @return array<Role>
     */
    public function findAll(): array;

    /**
     * Save Role aggregate.
     *
     * @param Role $role Role aggregate
     * @return void
     */
    public function save(Role $role): void;

    /**
     * Delete Role aggregate.
     *
     * @param RoleId $id Role ID
     * @return void
     */
    public function delete(RoleId $id): void;
}
