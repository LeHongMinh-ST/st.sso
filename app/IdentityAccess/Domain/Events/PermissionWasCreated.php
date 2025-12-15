<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when a permission is created.
 */
final class PermissionWasCreated
{
    /**
     * @param string $permissionId Permission ID (UUID)
     * @param string $code Permission code (unique identifier)
     * @param string $name Permission name
     */
    public function __construct(
        public readonly string $permissionId,
        public readonly string $code,
        public readonly string $name,
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
            'permission_id' => $this->permissionId,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
