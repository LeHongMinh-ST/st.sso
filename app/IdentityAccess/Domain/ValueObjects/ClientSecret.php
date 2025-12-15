<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Client Secret value object.
 * Represents an OAuth client secret.
 * Immutable and always valid.
 *
 * SECURITY: This class handles client secrets securely.
 */
final class ClientSecret
{
    private const MIN_LENGTH = 20;

    private string $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param string $value Client secret
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * Get client secret value (for string casting).
     *
     * SECURITY WARNING: This returns the actual secret.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create ClientSecret from string.
     *
     * @param string $value Client secret string
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Get client secret value as string.
     *
     * SECURITY WARNING: This returns the actual secret.
     * Use with caution and never log or expose this value.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Compare equality with another ClientSecret value object.
     * Uses timing-safe comparison for security.
     *
     * @param ClientSecret $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        // Use timing-safe comparison for security
        return hash_equals($this->value, $other->value);
    }

    /**
     * Validate client secret format and constraints.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Client secret cannot be empty');
        }

        $length = mb_strlen($value);
        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Client secret must be at least %d characters long', self::MIN_LENGTH)
            );
        }
    }
}
