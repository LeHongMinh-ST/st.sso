<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\DomainException;

/**
 * Exception thrown when authentication credentials are invalid.
 *
 * SECURITY: Generic error message to prevent user enumeration attacks.
 */
final class InvalidCredentialsException extends DomainException
{
    /**
     * Create exception for invalid credentials.
     *
     * SECURITY: Generic message to prevent information leakage.
     *
     * @return self
     */
    public static function invalid(): self
    {
        return new self('Invalid credentials');
    }

    /**
     * Create exception for invalid credentials with custom message.
     *
     * SECURITY: Use generic messages to prevent user enumeration.
     *
     * @param string $message Custom error message (should be generic)
     * @return self
     */
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
