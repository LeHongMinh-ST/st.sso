<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Phone number value object.
 * Represents a phone number with validation.
 */
final class PhoneNumber
{
    private ?string $value;

    private function __construct(?string $value)
    {
        if (null !== $value) {
            $this->validate($value);
            $this->value = trim($value);
        } else {
            $this->value = null;
        }
    }

    /**
     * Get phone number string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }

    /**
     * Create PhoneNumber from string.
     *
     * @param string|null $value Phone number string or null
     * @return self
     */
    public static function fromString(?string $value): self
    {
        return new self($value);
    }

    /**
     * Check if phone number is set.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        return null === $this->value;
    }

    /**
     * Check if this PhoneNumber equals another PhoneNumber.
     *
     * @param PhoneNumber $other
     * @return bool
     */
    public function equals(PhoneNumber $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get phone number string representation.
     *
     * @return string|null
     */
    public function toString(): ?string
    {
        return $this->value;
    }

    /**
     * Validate phone number.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Phone number cannot exceed 255 characters');
        }

        // Basic phone number validation (allows +, digits, spaces, hyphens, parentheses)
        if (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $trimmed)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
    }
}
