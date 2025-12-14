<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a user is assigned to a department.
 */
final class UserWasAssignedToDepartment
{
    /**
     * @param string $userId User ID (UUID)
     * @param string $departmentId Department ID (UUID)
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $departmentId,
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
            departmentId: $payload['department_id'],
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
            'department_id' => $this->departmentId,
        ];
    }
}
