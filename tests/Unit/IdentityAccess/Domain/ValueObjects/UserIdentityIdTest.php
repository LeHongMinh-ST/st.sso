<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Domain\ValueObjects;

use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserIdentityIdTest extends TestCase
{
    /**
     * Test that UserIdentityId can be created from string UUID.
     */
    public function test_can_create_from_string(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userIdentityId = UserIdentityId::fromString($uuid);

        $this->assertInstanceOf(UserIdentityId::class, $userIdentityId);
        $this->assertEquals($uuid, $userIdentityId->toString());
    }

    /**
     * Test that UserIdentityId can be generated.
     */
    public function test_can_generate(): void
    {
        $userIdentityId = UserIdentityId::generate();

        $this->assertInstanceOf(UserIdentityId::class, $userIdentityId);
        $this->assertNotEmpty($userIdentityId->toString());
    }

    /**
     * Test that generated IDs are unique.
     */
    public function test_generated_ids_are_unique(): void
    {
        $id1 = UserIdentityId::generate();
        $id2 = UserIdentityId::generate();

        $this->assertFalse($id1->equals($id2));
    }

    /**
     * Test that UserIdentityId can be created from OrganizationalStructure UserId.
     */
    public function test_can_create_from_organizational_structure_user_id(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $userIdentityId = UserIdentityId::fromOrganizationalStructureUserId($userId);

        $this->assertInstanceOf(UserIdentityId::class, $userIdentityId);
        $this->assertEquals($userId, $userIdentityId->toString());
    }

    /**
     * Test that invalid UUID throws exception.
     */
    public function test_invalid_uuid_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserIdentityId::fromString('not-a-valid-uuid');
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $id1 = UserIdentityId::fromString($uuid);
        $id2 = UserIdentityId::fromString($uuid);
        $id3 = UserIdentityId::generate();

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }

    /**
     * Test toString method.
     */
    public function test_to_string_method(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userIdentityId = UserIdentityId::fromString($uuid);

        $this->assertEquals($uuid, $userIdentityId->toString());
    }

    /**
     * Test __toString method.
     */
    public function test_magic_to_string_method(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userIdentityId = UserIdentityId::fromString($uuid);

        $this->assertEquals($uuid, (string) $userIdentityId);
    }
}
