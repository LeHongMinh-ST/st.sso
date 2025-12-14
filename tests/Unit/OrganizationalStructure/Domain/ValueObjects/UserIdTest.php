<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    /**
     * Test that UserId can be created from string UUID.
     */
    public function test_user_id_can_be_created_from_string(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $userId = UserId::fromString($uuidString);

        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals($uuidString, (string) $userId);
        $this->assertEquals($uuidString, $userId->toString());
    }

    /**
     * Test that UserId can be generated.
     */
    public function test_user_id_can_be_generated(): void
    {
        $userId = UserId::generate();

        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertNotEmpty((string) $userId);
    }

    /**
     * Test that invalid UUID throws exception.
     */
    public function test_invalid_uuid_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::fromString('not-a-uuid');
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $userId1 = UserId::fromString($uuidString);
        $userId2 = UserId::fromString($uuidString);
        $userId3 = UserId::generate();

        $this->assertTrue($userId1->equals($userId2));
        $this->assertFalse($userId1->equals($userId3));
    }

    /**
     * Test that generated UserIds are unique.
     */
    public function test_generated_user_ids_are_unique(): void
    {
        $userId1 = UserId::generate();
        $userId2 = UserId::generate();

        $this->assertFalse($userId1->equals($userId2));
    }
}
