<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

/**
 * Exception thrown when an entity is not found.
 */
final class EntityNotFoundException extends DomainException
{
    /**
     * @param string $entityType
     * @param string $identifier
     */
    public function __construct(string $entityType, string $identifier)
    {
        parent::__construct("{$entityType} not found with identifier: {$identifier}");
    }

    /**
     * Create exception for user not found.
     *
     * @param string $userId
     * @return self
     */
    public static function user(string $userId): self
    {
        return new self('User', $userId);
    }

    /**
     * Create exception for role not found.
     *
     * @param string $roleId
     * @return self
     */
    public static function role(string $roleId): self
    {
        return new self('Role', $roleId);
    }
}
