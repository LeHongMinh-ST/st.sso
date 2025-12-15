<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Exceptions;

use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

/**
 * Exception thrown when a Client is not found.
 */
final class ClientNotFoundException extends EntityNotFoundException
{
    /**
     * Create exception for client not found by ID.
     *
     * @param string $clientId Client ID
     * @return self
     */
    public static function withId(string $clientId): self
    {
        return new self('Client', $clientId);
    }
}
