<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a department is created.
 */
final class DepartmentWasCreated
{
    /**
     * @param string $departmentId Department ID (UUID)
     * @param string $name Department name
     * @param string $facultyId Faculty ID (UUID)
     */
    public function __construct(
        public readonly string $departmentId,
        public readonly string $name,
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
            departmentId: $payload['department_id'],
            name: $payload['name'],
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
            'department_id' => $this->departmentId,
            'name' => $this->name,
            'faculty_id' => $this->facultyId,
        ];
    }
}
