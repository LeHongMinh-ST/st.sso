<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Events;

/**
 * Event fired when a department is updated.
 */
final class DepartmentWasUpdated
{
    /**
     * @param string $departmentId Department ID (UUID)
     * @param array<string, mixed> $changes Changed fields
     */
    public function __construct(
        public readonly string $departmentId,
        public readonly array $changes,
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
            departmentId: $payload['department_id'],
            changes: $payload['changes'],
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
            'department_id' => $this->departmentId,
            'changes' => $this->changes,
        ];
    }
}
