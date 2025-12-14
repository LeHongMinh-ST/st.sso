<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a faculty is not found.
 */
final class FacultyNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for faculty not found by ID.
     *
     * @param string $facultyId Faculty ID (UUID)
     * @return self
     */
    public static function withId(string $facultyId): self
    {
        return new self("Faculty with ID {$facultyId} not found");
    }

    /**
     * Create exception for faculty not found by name.
     *
     * @param string $name Faculty name
     * @return self
     */
    public static function withName(string $name): self
    {
        return new self("Faculty with name {$name} not found");
    }
}
