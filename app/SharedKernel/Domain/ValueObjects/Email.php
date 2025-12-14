<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use InvalidArgumentException;
use Stringable;

/**
 * Email value object that can be used across all bounded contexts.
 * Immutable and always valid.
 */
final class Email implements Stringable
{
    private string $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param string $value
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = mb_strtolower(trim($value));
    }

    /**
     * Get email value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create Email from string.
     *
     * @param string $value
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Get email domain.
     *
     * @return string
     */
    public function domain(): string
    {
        $parts = explode('@', $this->value);
        return $parts[1] ?? '';
    }

    /**
     * Get email local part (before @).
     *
     * @return string
     */
    public function localPart(): string
    {
        $parts = explode('@', $this->value);
        return $parts[0] ?? '';
    }

    /**
     * Compare equality with another Email value object.
     *
     * @param Email $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Validate email format.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$value}");
        }
    }
}
