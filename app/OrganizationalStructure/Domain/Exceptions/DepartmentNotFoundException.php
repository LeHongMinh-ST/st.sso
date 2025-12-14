<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a department is not found.
 */
final class DepartmentNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for department not found by ID.
     *
     * @param string $departmentId Department ID (UUID)
     * @return self
     */
    public static function withId(string $departmentId): self
    {
        return new self("Department with ID {$departmentId} not found");
    }

    /**
     * Create exception for department not found by name.
     *
     * @param string $name Department name
     * @return self
     */
    public static function withName(string $name): self
    {
        return new self("Department with name {$name} not found");
    }
}
