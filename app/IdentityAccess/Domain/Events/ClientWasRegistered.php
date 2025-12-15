<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Events;

/**
 * Domain event fired when an OAuth2 client is registered.
 * SECURITY: This event does NOT contain client secret.
 */
final class ClientWasRegistered
{
    /**
     * @param string $clientId Client ID
     * @param string $name Client name
     * @param string $redirectUri Redirect URI
     */
    public function __construct(
        public readonly string $clientId,
        public readonly string $name,
        public readonly string $redirectUri,
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
            'client_id' => $this->clientId,
            'name' => $this->name,
            'redirect_uri' => $this->redirectUri,
        ];
    }
}
