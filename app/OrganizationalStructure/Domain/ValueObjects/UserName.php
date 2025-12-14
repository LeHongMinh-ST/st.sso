<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Username value object.
 * Represents a unique username identifier.
 */
final class UserName
{
    private string $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = trim($value);
    }

    /**
     * Get username string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create UserName from string.
     *
     * @param string $value Username string
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Check if this UserName equals another UserName.
     *
     * @param UserName $other
     * @return bool
     */
    public function equals(UserName $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get username string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Validate username.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Username cannot exceed 255 characters');
        }

        // Username should be alphanumeric with dots, underscores, or hyphens
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $trimmed)) {
            throw new InvalidArgumentException('Username can only contain letters, numbers, dots, underscores, and hyphens');
        }
    }
}
