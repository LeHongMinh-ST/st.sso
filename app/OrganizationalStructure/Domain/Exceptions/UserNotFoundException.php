<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a user is not found.
 */
final class UserNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for user not found by ID.
     *
     * @param string $userId User ID (UUID)
     * @return self
     */
    public static function withId(string $userId): self
    {
        return new self("User with ID {$userId} not found");
    }

    /**
     * Create exception for user not found by username.
     *
     * @param string $username Username
     * @return self
     */
    public static function withUsername(string $username): self
    {
        return new self("User with username {$username} not found");
    }

    /**
     * Create exception for user not found by email.
     *
     * @param string $email Email address
     * @return self
     */
    public static function withEmail(string $email): self
    {
        return new self("User with email {$email} not found");
    }
}
