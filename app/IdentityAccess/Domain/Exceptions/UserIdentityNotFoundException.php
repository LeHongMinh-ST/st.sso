<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a UserIdentity is not found.
 */
final class UserIdentityNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for user identity not found by ID.
     *
     * @param string $userIdentityId User Identity ID
     * @return self
     */
    public static function withId(string $userIdentityId): self
    {
        return new self('UserIdentity', $userIdentityId);
    }

    /**
     * Create exception for user identity not found by username.
     *
     * @param string $username Username
     * @return self
     */
    public static function withUsername(string $username): self
    {
        return new self('UserIdentity', "username: {$username}");
    }

    /**
     * Create exception for user identity not found by email.
     *
     * @param string $email Email address
     * @return self
     */
    public static function withEmail(string $email): self
    {
        return new self('UserIdentity', "email: {$email}");
    }
}
