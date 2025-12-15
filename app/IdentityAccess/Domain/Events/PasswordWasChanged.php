<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when a user password is changed.
 * SECURITY: This event does NOT contain password information.
 */
final class PasswordWasChanged
{
    /**
     * @param string $userIdentityId User Identity ID (UUID)
     */
    public function __construct(
        public readonly string $userIdentityId,
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
        ];
    }
}
