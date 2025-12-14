<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for updating user profile.
 */
final readonly class UpdateUserProfileDTO
{
    /**
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string|null $phone Phone number (nullable)
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone = null,
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
            firstName: $data['first_name'] ?? '',
            lastName: $data['last_name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? null,
        );
    }
}
