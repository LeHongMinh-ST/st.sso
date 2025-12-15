<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when a user identity is created.
 * This event is published to other bounded contexts (e.g., OrganizationalStructure).
 */
final class UserIdentityWasCreated
{
    /**
     * @param string $userIdentityId User Identity ID (UUID)
     * @param string $organizationalStructureUserId User ID from OrganizationalStructure context (UUID)
     * @param string $email User email address
     */
    public function __construct(
        public readonly string $userIdentityId,
        public readonly string $organizationalStructureUserId,
        public readonly string $email,
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
            'user_identity_id' => $this->userIdentityId,
            'organizational_structure_user_id' => $this->organizationalStructureUserId,
            'email' => $this->email,
        ];
    }
}
