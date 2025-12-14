<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for a single user row in import.
 */
final readonly class ImportUserRowDTO
{
    /**
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string $userCode User code (student code)
     * @param string|null $phone Phone number (nullable)
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $userCode,
        public ?string $phone = null,
    ) {
    }

    /**
     * Create DTO from array (Excel row).
     *
     * @param array<string, mixed> $row Excel row data
     * @return self
     */
    public static function fromArray(array $row): self
    {
        return new self(
            firstName: $row['ten'] ?? '',
            lastName: $row['ho'] ?? '',
            email: $row['email'] ?? '',
            userCode: $row['ma_sinh_vien'] ?? '',
            phone: $row['so_dien_thoai'] ?? null,
        );
    }
}
