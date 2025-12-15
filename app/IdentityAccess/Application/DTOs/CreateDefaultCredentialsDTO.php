<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for creating default credentials.
 * Used when a user is created in OrganizationalStructure context.
 */
final class CreateDefaultCredentialsDTO
{
    /**
     * @param string $userId User ID from OrganizationalStructure context (UUID)
     * @param string $email User email address
     * @param string $defaultPassword Default password (will be hashed)
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $defaultPassword,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'] ?? '',
            $data['email'] ?? '',
            $data['default_password'] ?? 'password',
        );
    }
}
