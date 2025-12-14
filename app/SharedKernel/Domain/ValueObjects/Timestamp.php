<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Stringable;

/**
 * Timestamp value object that can be used across all bounded contexts.
 * Immutable and always includes timezone information.
 */
final class Timestamp implements Stringable
{
    private DateTimeImmutable $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param DateTimeImmutable $value
     */
    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * Get timestamp as string in ISO 8601 format.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    /**
     * Create Timestamp from DateTimeImmutable.
     *
     * @param DateTimeImmutable $dateTime
     * @return self
     */
    public static function fromDateTime(DateTimeImmutable $dateTime): self
    {
        return new self($dateTime);
    }

    /**
     * Create Timestamp from string.
     *
     * @param string $value
     * @param string|null $timezone
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value, ?string $timezone = null): self
    {
        try {
            $dateTime = new DateTimeImmutable(
                $value,
                $timezone ? new DateTimeZone($timezone) : null
            );
            return new self($dateTime);
        } catch (Exception $e) {
            throw new InvalidArgumentException("Invalid timestamp format: {$value}", 0, $e);
        }
    }

    /**
     * Create Timestamp for current time.
     *
     * @param string|null $timezone
     * @return self
     */
    public static function now(?string $timezone = null): self
    {
        $dateTime = new DateTimeImmutable('now', $timezone ? new DateTimeZone($timezone) : null);
        return new self($dateTime);
    }

    /**
     * Get timestamp as DateTimeImmutable.
     *
     * @return DateTimeImmutable
     */
    public function toDateTime(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Get timestamp as string in specific format.
     *
     * @param string $format
     * @return string
     */
    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    /**
     * Compare equality with another Timestamp value object.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    /**
     * Check if this timestamp is before another.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    /**
     * Check if this timestamp is after another.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }
}
