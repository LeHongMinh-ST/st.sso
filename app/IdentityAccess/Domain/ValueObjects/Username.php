<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use Stringable;

/**
 * Username value object.
 * Represents a unique username for authentication.
 * Immutable and always valid.
 */
final class Username implements Stringable
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 50;
    private const VALID_PATTERN = '/^[a-zA-Z0-9._-]+$/';

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
        $this->value = trim($value);
    }

    /**
     * Get username value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create Username from string.
     *
     * @param string $value Username string
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Get username value as string (explicit method).
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Compare equality with another Username value object.
     *
     * @param Username $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Validate username format and constraints.
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

        $length = mb_strlen($trimmed);
        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Username must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Username must not exceed %d characters', self::MAX_LENGTH)
            );
        }

        if (!preg_match(self::VALID_PATTERN, $trimmed)) {
            throw new InvalidArgumentException(
                'Username can only contain letters, numbers, dots, underscores, and hyphens'
            );
        }
    }
}
