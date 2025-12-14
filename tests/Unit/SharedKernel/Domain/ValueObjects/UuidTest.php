<?php

declare(strict_types=1);

namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\ValueObjects\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    /**
     * Test that valid UUID can be created from string.
     */
    public function test_valid_uuid_can_be_created_from_string(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = Uuid::fromString($uuidString);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals($uuidString, (string) $uuid);
        $this->assertEquals($uuidString, $uuid->toString());
    }

    /**
     * Test that UUID can be generated.
     */
    public function test_uuid_can_be_generated(): void
    {
        $uuid = Uuid::generate();

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertNotEmpty((string) $uuid);
    }

    /**
     * Test that invalid UUID throws exception.
     */
    public function test_invalid_uuid_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID format');

        Uuid::fromString('not-a-uuid');
    }

    /**
     * Test that empty string throws exception.
     */
    public function test_empty_string_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Uuid::fromString('');
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid1 = Uuid::fromString($uuidString);
        $uuid2 = Uuid::fromString($uuidString);
        $uuid3 = Uuid::generate();

        $this->assertTrue($uuid1->equals($uuid2));
        $this->assertFalse($uuid1->equals($uuid3));
    }

    /**
     * Test that generated UUIDs are unique.
     */
    public function test_generated_uuids_are_unique(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();

        $this->assertFalse($uuid1->equals($uuid2));
    }
}
