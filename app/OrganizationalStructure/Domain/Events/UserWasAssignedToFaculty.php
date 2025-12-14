<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a user is assigned to a faculty.
 */
final class UserWasAssignedToFaculty
{
    /**
     * @param string $userId User ID (UUID)
     * @param string $facultyId Faculty ID (UUID)
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $facultyId,
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
            facultyId: $payload['faculty_id'],
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
            'faculty_id' => $this->facultyId,
        ];
    }
}
