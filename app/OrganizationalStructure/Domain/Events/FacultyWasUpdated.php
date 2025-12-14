<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a faculty is updated.
 */
final class FacultyWasUpdated
{
    /**
     * @param string $facultyId Faculty ID (UUID)
     * @param array<string, mixed> $changes Changed fields
     */
    public function __construct(
        public readonly string $facultyId,
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
            facultyId: $payload['faculty_id'],
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
            'faculty_id' => $this->facultyId,
            'changes' => $this->changes,
        ];
    }
}
