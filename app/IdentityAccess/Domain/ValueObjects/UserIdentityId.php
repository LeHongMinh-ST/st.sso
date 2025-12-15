<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use App\SharedKernel\Domain\ValueObjects\Uuid;

/**
 * User Identity ID value object.
 * Links to UserId from OrganizationalStructure context.
 * This represents the identity part of a user (authentication credentials).
 */
final class UserIdentityId
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
     * Create UserIdentityId from string UUID.
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
     * Generate a new UserIdentityId.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::generate());
    }

    /**
     * Create from OrganizationalStructure UserId.
     * This links the identity context with the organizational structure context.
     *
     * @param string $userId User ID from OrganizationalStructure context
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromOrganizationalStructureUserId(string $userId): self
    {
        return new self(Uuid::fromString($userId));
    }

    /**
     * Compare equality with another UserIdentityId.
     *
     * @param UserIdentityId $other
     * @return bool
     */
    public function equals(UserIdentityId $other): bool
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
