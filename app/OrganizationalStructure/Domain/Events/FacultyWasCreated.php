<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a faculty is created.
 */
final class FacultyWasCreated
{
    /**
     * @param string $facultyId Faculty ID (UUID)
     * @param string $name Faculty name
     */
    public function __construct(
        public readonly string $facultyId,
        public readonly string $name,
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
            name: $payload['name'],
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
            'name' => $this->name,
        ];
    }
}
