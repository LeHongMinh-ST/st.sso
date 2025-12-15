<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when a permission is assigned to a role.
 */
final class PermissionWasAssignedToRole
{
    /**
     * @param string $roleId Role ID (UUID)
     * @param string $permissionId Permission ID (UUID)
     */
    public function __construct(
        public readonly string $roleId,
        public readonly string $permissionId,
    ) {
    }

    /**
     * Convert event to payload array for outbox storage.
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'role_id' => $this->roleId,
            'permission_id' => $this->permissionId,
        ];
    }
}
