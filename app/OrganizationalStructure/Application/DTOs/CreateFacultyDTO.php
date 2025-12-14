<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for creating a faculty.
 */
final readonly class CreateFacultyDTO
{
    /**
     * @param string $name Faculty name
     * @param string|null $description Description (nullable)
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
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
            description: $data['description'] ?? null,
        );
    }
}
