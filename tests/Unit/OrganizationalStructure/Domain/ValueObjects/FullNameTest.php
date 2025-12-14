<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FullNameTest extends TestCase
{
    /**
     * Test that full name can be created from parts.
     */
    public function test_full_name_can_be_created_from_parts(): void
    {
        $fullName = FullName::fromParts('John', 'Doe');

        $this->assertInstanceOf(FullName::class, $fullName);
        $this->assertEquals('John', $fullName->firstName());
        $this->assertEquals('Doe', $fullName->lastName());
        $this->assertEquals('Doe John', $fullName->fullName());
    }

    /**
     * Test that empty first name throws exception.
     */
    public function test_empty_first_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty');

        FullName::fromParts('', 'Doe');
    }

    /**
     * Test that empty last name throws exception.
     */
    public function test_empty_last_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty');

        FullName::fromParts('John', '');
    }

    /**
     * Test that whitespace-only first name throws exception.
     */
    public function test_whitespace_only_first_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty');

        FullName::fromParts('   ', 'Doe');
    }

    /**
     * Test that whitespace-only last name throws exception.
     */
    public function test_whitespace_only_last_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty');

        FullName::fromParts('John', '   ');
    }

    /**
     * Test that first name too long throws exception.
     */
    public function test_first_name_too_long_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot exceed 255 characters');

        $longName = str_repeat('a', 256);
        FullName::fromParts($longName, 'Doe');
    }

    /**
     * Test that last name too long throws exception.
     */
    public function test_last_name_too_long_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot exceed 255 characters');

        $longName = str_repeat('a', 256);
        FullName::fromParts('John', $longName);
    }

    /**
     * Test that full name trims whitespace.
     */
    public function test_full_name_trims_whitespace(): void
    {
        $fullName = FullName::fromParts('  John  ', '  Doe  ');

        $this->assertEquals('John', $fullName->firstName());
        $this->assertEquals('Doe', $fullName->lastName());
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $fullName1 = FullName::fromParts('John', 'Doe');
        $fullName2 = FullName::fromParts('John', 'Doe');
        $fullName3 = FullName::fromParts('Jane', 'Doe');

        $this->assertTrue($fullName1->equals($fullName2));
        $this->assertFalse($fullName1->equals($fullName3));
    }

    /**
     * Test toString method returns full name format.
     */
    public function test_to_string_returns_full_name_format(): void
    {
        $fullName = FullName::fromParts('John', 'Doe');

        $this->assertEquals('Doe John', (string) $fullName);
    }
}
