<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for creating a user.
 */
final readonly class CreateUserDTO
{
    /**
     * @param string $userName Username
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string|null $userCode User code (nullable)
     * @param string|null $phone Phone number (nullable)
     * @param string|null $facultyId Faculty ID (UUID, nullable)
     * @param string|null $departmentId Department ID (UUID, nullable)
     */
    public function __construct(
        public string $userName,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $userCode = null,
        public ?string $phone = null,
        public ?string $facultyId = null,
        public ?string $departmentId = null,
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
            userName: $data['user_name'] ?? '',
            firstName: $data['first_name'] ?? '',
            lastName: $data['last_name'] ?? '',
            email: $data['email'] ?? '',
            userCode: $data['code'] ?? $data['user_code'] ?? null,
            phone: $data['phone'] ?? null,
            facultyId: $data['faculty_id'] ?? null,
            departmentId: $data['department_id'] ?? null,
        );
    }
}
