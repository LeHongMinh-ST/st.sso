<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for user data output.
 */
final readonly class UserDTO
{
    /**
     * @param string $id User ID (UUID)
     * @param string $userName Username
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string|null $userCode User code (nullable)
     * @param string|null $phone Phone number (nullable)
     * @param string|null $facultyId Faculty ID (UUID, nullable)
     * @param string|null $departmentId Department ID (UUID, nullable)
     * @param string|null $facultyName Faculty name (nullable)
     * @param string|null $departmentName Department name (nullable)
     */
    public function __construct(
        public string $id,
        public string $userName,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $userCode = null,
        public ?string $phone = null,
        public ?string $facultyId = null,
        public ?string $departmentId = null,
        public ?string $facultyName = null,
        public ?string $departmentName = null,
    ) {
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function fullName(): string
    {
        return "{$this->lastName} {$this->firstName}";
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->id,
            'user_name' => $this->userName,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'full_name' => $this->fullName(),
            'email' => $this->email,
            'code' => $this->userCode,
            'phone' => $this->phone,
            'faculty_id' => $this->facultyId,
            'department_id' => $this->departmentId,
            'faculty' => $this->facultyName ? ['name' => $this->facultyName] : null,
            'department' => $this->departmentName ? ['name' => $this->departmentName] : null,
        ];
    }
}
