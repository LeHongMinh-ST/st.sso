<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\ValueObjects\Uuid;

/**
 * User ID value object.
 * Wraps Uuid from SharedKernel for type safety.
 */
final class UserId
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
     * Create UserId from string UUID.
     *
     * @param string $value UUID string
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    /**
     * Generate a new UserId.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::generate());
    }

    /**
     * Check if this UserId equals another UserId.
     *
     * @param UserId $other
     * @return bool
     */
    public function equals(UserId $other): bool
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
