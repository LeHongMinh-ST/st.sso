<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Domain\Exceptions\InsufficientPermissionException;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\RoleId;

/**
 * Use case for checking if a user has a specific permission.
 *
 * This use case checks permissions through roles assigned to the user.
 */
final class CheckPermissionUseCase
{
    /**
     * @param RoleRepositoryInterface $roleRepository
     * @param PermissionRepositoryInterface $permissionRepository
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionRepositoryInterface $permissionRepository,
    ) {
    }

    /**
     * Check if user has permission through roles.
     *
     * Note: This is a simplified version. In a full implementation,
     * you would need to check user's roles first, then check if any role has the permission.
     *
     * @param array<string> $userRoleIds Array of Role IDs (UUID strings) assigned to user
     * @param string $permissionCode Permission code to check
     * @return bool True if user has permission, false otherwise
     * @throws InsufficientPermissionException
     */
    public function execute(array $userRoleIds, string $permissionCode): bool
    {
        // Find permission by code
        $permission = $this->permissionRepository->findByCode($permissionCode);

        if (null === $permission) {
            throw InsufficientPermissionException::forPermission($permissionCode);
        }

        $permissionId = $permission->id()->toString();

        // Check if any of user's roles has this permission
        foreach ($userRoleIds as $roleIdString) {
            $roleId = RoleId::fromString($roleIdString);
            $role = $this->roleRepository->findById($roleId);

            if (null !== $role && $role->hasPermission($permissionId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has permission and throw exception if not.
     *
     * @param array<string> $userRoleIds Array of Role IDs assigned to user
     * @param string $permissionCode Permission code to check
     * @return void
     * @throws InsufficientPermissionException
     */
    public function ensureHasPermission(array $userRoleIds, string $permissionCode): void
    {
        if (!$this->execute($userRoleIds, $permissionCode)) {
            throw InsufficientPermissionException::forPermission($permissionCode);
        }
    }
}
