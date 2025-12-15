<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when an access token is revoked.
 * SECURITY: This event does NOT contain the actual token value.
 */
final class TokenWasRevoked
{
    /**
     * @param string $userIdentityId User Identity ID (UUID)
     * @param string $clientId Client ID
     */
    public function __construct(
        public readonly string $userIdentityId,
        public readonly string $clientId,
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
            'client_id' => $this->clientId,
        ];
    }
}
