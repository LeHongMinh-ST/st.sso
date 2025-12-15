<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when an access token is issued.
 * SECURITY: This event does NOT contain the actual token value.
 */
final class TokenWasIssued
{
    /**
     * @param string $userIdentityId User Identity ID (UUID)
     * @param string $clientId Client ID
     * @param array<string> $scopes Token scopes
     */
    public function __construct(
        public readonly string $userIdentityId,
        public readonly string $clientId,
        public readonly array $scopes = [],
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
            'scopes' => $this->scopes,
        ];
    }
}
