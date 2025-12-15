<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repositories;

use App\IdentityAccess\Domain\Aggregates\Permission;
use App\IdentityAccess\Domain\ValueObjects\PermissionId;

/**
 * Permission Repository Interface.
 * Defines contract for persisting and retrieving Permission aggregates.
 */
interface PermissionRepositoryInterface
{
    /**
     * Generate a new Permission ID.
     *
     * @return PermissionId
     */
    public function nextIdentity(): PermissionId;

    /**
     * Find Permission by ID.
     *
     * @param PermissionId $id Permission ID
     * @return Permission|null
     */
    public function findById(PermissionId $id): ?Permission;

    /**
     * Find Permission by code.
     *
     * @param string $code Permission code
     * @return Permission|null
     */
    public function findByCode(string $code): ?Permission;

    /**
     * Find all permissions.
     *
     * @return array<Permission>
     */
    public function findAll(): array;

    /**
     * Find permissions by group ID.
     *
     * @param string $permissionGroupId Permission group ID
     * @return array<Permission>
     */
    public function findByGroupId(string $permissionGroupId): array;

    /**
     * Save Permission aggregate.
     *
     * @param Permission $permission Permission aggregate
     * @return void
     */
    public function save(Permission $permission): void;

    /**
     * Delete Permission aggregate.
     *
     * @param PermissionId $id Permission ID
     * @return void
     */
    public function delete(PermissionId $id): void;
}
