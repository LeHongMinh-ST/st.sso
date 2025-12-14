<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserNameTest extends TestCase
{
    /**
     * Test that username can be created from string.
     */
    public function test_username_can_be_created_from_string(): void
    {
        $username = UserName::fromString('john.doe');

        $this->assertInstanceOf(UserName::class, $username);
        $this->assertEquals('john.doe', (string) $username);
    }

    /**
     * Test that empty username throws exception.
     */
    public function test_empty_username_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        UserName::fromString('');
    }

    /**
     * Test that username too long throws exception.
     */
    public function test_username_too_long_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot exceed 255 characters');

        $longUsername = str_repeat('a', 256);
        UserName::fromString($longUsername);
    }

    /**
     * Test that invalid characters throw exception.
     */
    public function test_invalid_characters_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username can only contain letters, numbers, dots, underscores, and hyphens');

        UserName::fromString('john@doe');
    }

    /**
     * Test that username trims whitespace.
     */
    public function test_username_trims_whitespace(): void
    {
        $username = UserName::fromString('  john.doe  ');

        $this->assertEquals('john.doe', (string) $username);
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $username1 = UserName::fromString('john.doe');
        $username2 = UserName::fromString('john.doe');
        $username3 = UserName::fromString('jane.doe');

        $this->assertTrue($username1->equals($username2));
        $this->assertFalse($username1->equals($username3));
    }

    /**
     * Test valid username formats.
     */
    public function test_valid_username_formats(): void
    {
        $validUsernames = ['john.doe', 'john_doe', 'john-doe', 'john123', 'john.doe_123'];

        foreach ($validUsernames as $validUsername) {
            $username = UserName::fromString($validUsername);
            $this->assertInstanceOf(UserName::class, $username);
        }
    }
}
