<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a user profile is updated.
 */
final class UserWasUpdated
{
    /**
     * @param string $userId User ID (UUID)
     * @param array<string, mixed> $changes Changed fields
     */
    public function __construct(
        public readonly string $userId,
        public readonly array $changes,
    ) {
    }

    /**
     * Reconstruct event from payload array.
     *
     * @param array<string, mixed> $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            userId: $payload['user_id'],
            changes: $payload['changes'],
        );
    }

    /**
     * Convert event to payload array for serialization.
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'changes' => $this->changes,
        ];
    }
}
