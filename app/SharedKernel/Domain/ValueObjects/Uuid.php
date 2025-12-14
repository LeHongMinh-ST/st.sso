<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * UUID value object that can be used across all bounded contexts.
 * Immutable and always valid.
 */
final class Uuid implements Stringable
{
    private UuidInterface $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param UuidInterface $value
     */
    private function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    /**
     * Create UUID from string.
     *
     * @param string $value
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException("Invalid UUID format: {$value}");
        }

        return new self(RamseyUuid::fromString($value));
    }

    /**
     * Generate a new UUID.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4());
    }

    /**
     * Get UUID value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->toString();
    }

    /**
     * Get UUID value as string (explicit method).
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value->toString();
    }

    /**
     * Compare equality with another Uuid value object.
     *
     * @param Uuid $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value->equals($other->value);
    }
}
