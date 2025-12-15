<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a Permission is not found.
 */
final class PermissionNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for permission not found by ID.
     *
     * @param string $permissionId Permission ID
     * @return self
     */
    public static function withId(string $permissionId): self
    {
        return new self('Permission', $permissionId);
    }

    /**
     * Create exception for permission not found by code.
     *
     * @param string $code Permission code
     * @return self
     */
    public static function withCode(string $code): self
    {
        return new self('Permission', "code: {$code}");
    }
}
