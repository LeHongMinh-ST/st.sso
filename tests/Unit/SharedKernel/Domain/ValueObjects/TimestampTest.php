<?php

declare(strict_types=1);

namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\ValueObjects\Timestamp;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TimestampTest extends TestCase
{
    /**
     * Test that timestamp can be created from DateTimeImmutable.
     */
    public function test_timestamp_can_be_created_from_datetime(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01 12:00:00');
        $timestamp = Timestamp::fromDateTime($dateTime);

        $this->assertInstanceOf(Timestamp::class, $timestamp);
        $this->assertEquals($dateTime, $timestamp->toDateTime());
    }

    /**
     * Test that timestamp can be created from string.
     */
    public function test_timestamp_can_be_created_from_string(): void
    {
        $timestamp = Timestamp::fromString('2024-01-01 12:00:00');

        $this->assertInstanceOf(Timestamp::class, $timestamp);
    }

    /**
     * Test that timestamp can be created for current time.
     */
    public function test_timestamp_can_be_created_for_current_time(): void
    {
        $timestamp = Timestamp::now();

        $this->assertInstanceOf(Timestamp::class, $timestamp);
        $this->assertInstanceOf(DateTimeImmutable::class, $timestamp->toDateTime());
    }

    /**
     * Test that invalid timestamp string throws exception.
     */
    public function test_invalid_timestamp_string_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp format');

        Timestamp::fromString('invalid-date');
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-01 12:00:00');
        $timestamp1 = Timestamp::fromDateTime($dateTime);
        $timestamp2 = Timestamp::fromDateTime($dateTime);

        $this->assertTrue($timestamp1->equals($timestamp2));
    }

    /**
     * Test isBefore method.
     */
    public function test_is_before_method(): void
    {
        $timestamp1 = Timestamp::fromString('2024-01-01 12:00:00');
        $timestamp2 = Timestamp::fromString('2024-01-01 13:00:00');

        $this->assertTrue($timestamp1->isBefore($timestamp2));
        $this->assertFalse($timestamp2->isBefore($timestamp1));
    }

    /**
     * Test isAfter method.
     */
    public function test_is_after_method(): void
    {
        $timestamp1 = Timestamp::fromString('2024-01-01 12:00:00');
        $timestamp2 = Timestamp::fromString('2024-01-01 13:00:00');

        $this->assertTrue($timestamp2->isAfter($timestamp1));
        $this->assertFalse($timestamp1->isAfter($timestamp2));
    }

    /**
     * Test format method.
     */
    public function test_format_method(): void
    {
        $timestamp = Timestamp::fromString('2024-01-01 12:00:00');
        $formatted = $timestamp->format('Y-m-d');

        $this->assertEquals('2024-01-01', $formatted);
    }

    /**
     * Test toString returns ISO 8601 format.
     */
    public function test_to_string_returns_iso_8601_format(): void
    {
        $timestamp = Timestamp::fromString('2024-01-01T12:00:00+00:00');
        $string = (string) $timestamp;

        $this->assertStringContainsString('2024-01-01T12:00:00', $string);
    }
}
