<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\ValueObjects\Uuid;

/**
 * Department ID value object.
 * Wraps Uuid from SharedKernel for type safety.
 */
final class DepartmentId
{
    private Uuid $uuid;

    private function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get UUID string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create DepartmentId from string UUID.
     *
     * @param string $value UUID string
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    /**
     * Generate a new DepartmentId.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::generate());
    }

    /**
     * Check if this DepartmentId equals another DepartmentId.
     *
     * @param DepartmentId $other
     * @return bool
     */
    public function equals(DepartmentId $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    /**
     * Get UUID string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid->toString();
    }
}
