<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\DomainException;

/**
 * Exception thrown when a user does not have sufficient permissions.
 */
final class InsufficientPermissionException extends DomainException
{
    /**
     * Create exception for insufficient permission.
     *
     * @param string $permissionCode Permission code that is required
     * @return self
     */
    public static function forPermission(string $permissionCode): self
    {
        return new self("Insufficient permission: {$permissionCode}");
    }

    /**
     * Create exception for insufficient permission with custom message.
     *
     * @param string $message Custom error message
     * @return self
     */
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
