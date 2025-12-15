<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a Role is not found.
 */
final class RoleNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for role not found by ID.
     *
     * @param string $roleId Role ID
     * @return self
     */
    public static function withId(string $roleId): self
    {
        return new self('Role', $roleId);
    }

    /**
     * Create exception for role not found by name.
     *
     * @param string $name Role name
     * @return self
     */
    public static function withName(string $name): self
    {
        return new self('Role', "name: {$name}");
    }
}
