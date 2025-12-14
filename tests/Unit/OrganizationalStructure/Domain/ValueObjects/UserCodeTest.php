<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserCodeTest extends TestCase
{
    /**
     * Test that user code can be created from string.
     */
    public function test_user_code_can_be_created_from_string(): void
    {
        $code = UserCode::fromString('ST001');

        $this->assertInstanceOf(UserCode::class, $code);
        $this->assertEquals('ST001', (string) $code);
    }

    /**
     * Test that empty code throws exception.
     */
    public function test_empty_code_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User code cannot be empty');

        UserCode::fromString('');
    }

    /**
     * Test that code too long throws exception.
     */
    public function test_code_too_long_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User code cannot exceed 255 characters');

        $longCode = str_repeat('a', 256);
        UserCode::fromString($longCode);
    }

    /**
     * Test that invalid format throws exception.
     */
    public function test_invalid_format_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User code must be alphanumeric');

        UserCode::fromString('ST-001');
    }

    /**
     * Test that code trims whitespace.
     */
    public function test_code_trims_whitespace(): void
    {
        $code = UserCode::fromString('  ST001  ');

        $this->assertEquals('ST001', (string) $code);
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $code1 = UserCode::fromString('ST001');
        $code2 = UserCode::fromString('ST001');
        $code3 = UserCode::fromString('ST002');

        $this->assertTrue($code1->equals($code2));
        $this->assertFalse($code1->equals($code3));
    }
}
