<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use App\SharedKernel\Domain\ValueObjects\Uuid;

/**
 * Permission ID value object.
 * Represents a unique identifier for a Permission.
 */
final class PermissionId
{
    private Uuid $uuid;

    /**
     * Private constructor to enforce immutability.
     *
     * @param Uuid $uuid
     */
    private function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get UUID as string (for string casting).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create PermissionId from string UUID.
     *
     * @param string $value UUID string
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    /**
     * Generate a new PermissionId.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::generate());
    }

    /**
     * Compare equality with another PermissionId.
     *
     * @param PermissionId $other
     * @return bool
     */
    public function equals(PermissionId $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    /**
     * Get UUID as string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid->toString();
    }
}
