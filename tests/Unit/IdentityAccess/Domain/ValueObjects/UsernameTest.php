<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Domain\ValueObjects;

use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UsernameTest extends TestCase
{
    /**
     * Test that valid username can be created.
     */
    public function test_valid_username_can_be_created(): void
    {
        $username = Username::fromString('testuser');

        $this->assertInstanceOf(Username::class, $username);
        $this->assertEquals('testuser', (string) $username);
    }

    /**
     * Test that username trims whitespace.
     */
    public function test_username_trims_whitespace(): void
    {
        $username = Username::fromString('  testuser  ');

        $this->assertEquals('testuser', (string) $username);
    }

    /**
     * Test that empty username throws exception.
     */
    public function test_empty_username_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        Username::fromString('');
    }

    /**
     * Test that whitespace-only username throws exception.
     */
    public function test_whitespace_only_username_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        Username::fromString('   ');
    }

    /**
     * Test that too short username throws exception.
     */
    public function test_too_short_username_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username must be at least 3 characters long');

        Username::fromString('ab');
    }

    /**
     * Test that username with 2 characters throws exception.
     */
    public function test_username_with_2_characters_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username must be at least 3 characters long');

        Username::fromString('ab');
    }

    /**
     * Test that username with 3 characters is valid.
     */
    public function test_username_with_3_characters_is_valid(): void
    {
        $username = Username::fromString('abc');

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test that too long username throws exception.
     */
    public function test_too_long_username_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username must not exceed 50 characters');

        Username::fromString(str_repeat('a', 51));
    }

    /**
     * Test that username with 50 characters is valid.
     */
    public function test_username_with_50_characters_is_valid(): void
    {
        $username = Username::fromString(str_repeat('a', 50));

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test that username with invalid characters throws exception.
     */
    public function test_username_with_invalid_characters_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username can only contain letters, numbers, dots, underscores, and hyphens');

        Username::fromString('test@user');
    }

    /**
     * Test that username with spaces throws exception.
     */
    public function test_username_with_spaces_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username can only contain letters, numbers, dots, underscores, and hyphens');

        Username::fromString('test user');
    }

    /**
     * Test that username with dots is valid.
     */
    public function test_username_with_dots_is_valid(): void
    {
        $username = Username::fromString('test.user');

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test that username with underscores is valid.
     */
    public function test_username_with_underscores_is_valid(): void
    {
        $username = Username::fromString('test_user');

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test that username with hyphens is valid.
     */
    public function test_username_with_hyphens_is_valid(): void
    {
        $username = Username::fromString('test-user');

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test that username with numbers is valid.
     */
    public function test_username_with_numbers_is_valid(): void
    {
        $username = Username::fromString('test123');

        $this->assertInstanceOf(Username::class, $username);
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $username1 = Username::fromString('testuser');
        $username2 = Username::fromString('testuser');
        $username3 = Username::fromString('otheruser');

        $this->assertTrue($username1->equals($username2));
        $this->assertFalse($username1->equals($username3));
    }

    /**
     * Test toString method.
     */
    public function test_to_string_method(): void
    {
        $username = Username::fromString('testuser');

        $this->assertEquals('testuser', $username->toString());
    }
}
