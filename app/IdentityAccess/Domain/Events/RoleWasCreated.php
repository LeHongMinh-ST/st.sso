<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when a role is created.
 */
final class RoleWasCreated
{
    /**
     * @param string $roleId Role ID (UUID)
     * @param string $name Role name
     * @param string $displayName Role display name
     */
    public function __construct(
        public readonly string $roleId,
        public readonly string $name,
        public readonly string $displayName,
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
            'name' => $this->name,
            'display_name' => $this->displayName,
        ];
    }
}
