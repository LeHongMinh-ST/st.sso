<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a user is created.
 */
final class UserWasCreated
{
    /**
     * @param string $userId User ID (UUID)
     * @param string $email User email
     * @param string $fullName User full name
     * @param string $userName Username
     * @param string|null $userCode User code (nullable)
     * @param string|null $facultyId Faculty ID (UUID, nullable)
     * @param string|null $departmentId Department ID (UUID, nullable)
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $fullName,
        public readonly string $userName,
        public readonly ?string $userCode = null,
        public readonly ?string $facultyId = null,
        public readonly ?string $departmentId = null,
    ) {
    }

    /**
     * Reconstruct event from payload array.
     *
     * @param array<string, mixed> $payload
     * @return self
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            userId: $payload['user_id'],
            email: $payload['email'],
            fullName: $payload['full_name'],
            userName: $payload['user_name'],
            userCode: $payload['user_code'] ?? null,
            facultyId: $payload['faculty_id'] ?? null,
            departmentId: $payload['department_id'] ?? null,
        );
    }

    /**
     * Convert event to payload array for serialization.
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'full_name' => $this->fullName,
            'user_name' => $this->userName,
            'user_code' => $this->userCode,
            'faculty_id' => $this->facultyId,
            'department_id' => $this->departmentId,
        ];
    }
}
