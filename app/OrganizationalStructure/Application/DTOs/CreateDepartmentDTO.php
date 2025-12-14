<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for creating a department.
 */
final readonly class CreateDepartmentDTO
{
    /**
     * @param string $name Department name
     * @param string $facultyId Faculty ID (UUID)
     */
    public function __construct(
        public string $name,
        public string $facultyId,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            facultyId: $data['faculty_id'] ?? '',
        );
    }
}
