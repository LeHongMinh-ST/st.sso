<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Client ID value object.
 * Represents OAuth2 client identifier.
 * Note: Laravel Passport uses string IDs for clients.
 */
final class ClientId
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
        $this->value = $value;
    }

    /**
     * Get client ID as string (for string casting).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create ClientId from string.
     *
     * @param string $value Client ID string
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Get client ID as string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Compare equality with another ClientId value object.
     *
     * @param ClientId $other
     * @return bool
     */
    public function equals(ClientId $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Validate client ID format.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Client ID cannot be empty');
        }

        // Laravel Passport client IDs are typically numeric strings
        // But we allow alphanumeric for flexibility
        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Client ID cannot exceed 255 characters');
        }
    }
}
