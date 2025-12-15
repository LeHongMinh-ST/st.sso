<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * DTO for removing a permission from a role.
 */
final readonly class RemovePermissionFromRoleDTO
{
    /**
     * @param string $roleId Role ID (UUID)
     * @param string $permissionId Permission ID (UUID)
     */
    public function __construct(
        public string $roleId,
        public string $permissionId,
    ) {
    }
}
