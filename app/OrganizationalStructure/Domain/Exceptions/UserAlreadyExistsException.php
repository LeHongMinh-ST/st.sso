<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\DomainException;

/**
 * Exception thrown when a user already exists.
 */
final class UserAlreadyExistsException extends DomainException
{
    /**
     * Create exception for user already exists by email.
     *
     * @param string $email Email address
     * @return self
     */
    public static function withEmail(string $email): self
    {
        return new self("User with email {$email} already exists");
    }

    /**
     * Create exception for user already exists by username.
     *
     * @param string $userName Username
     * @return self
     */
    public static function withUserName(string $userName): self
    {
        return new self("User with username {$userName} already exists");
    }

    /**
     * Create exception for user already exists by user code.
     *
     * @param string $userCode User code
     * @return self
     */
    public static function withUserCode(string $userCode): self
    {
        return new self("User with code {$userCode} already exists");
    }
}
