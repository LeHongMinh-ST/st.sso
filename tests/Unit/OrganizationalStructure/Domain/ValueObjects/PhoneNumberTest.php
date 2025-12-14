<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PhoneNumberTest extends TestCase
{
    /**
     * Test that phone number can be created from string.
     */
    public function test_phone_number_can_be_created_from_string(): void
    {
        $phoneNumber = PhoneNumber::fromString('+84123456789');

        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
        $this->assertEquals('+84123456789', (string) $phoneNumber);
        $this->assertFalse($phoneNumber->isNull());
    }

    /**
     * Test that phone number can be null.
     */
    public function test_phone_number_can_be_null(): void
    {
        $phoneNumber = PhoneNumber::fromString(null);

        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
        $this->assertTrue($phoneNumber->isNull());
        $this->assertNull($phoneNumber->toString());
        $this->assertEquals('', (string) $phoneNumber);
    }

    /**
     * Test that empty phone number throws exception.
     */
    public function test_empty_phone_number_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone number cannot be empty');

        PhoneNumber::fromString('');
    }

    /**
     * Test that phone number too long throws exception.
     */
    public function test_phone_number_too_long_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone number cannot exceed 255 characters');

        $longPhone = str_repeat('1', 256);
        PhoneNumber::fromString($longPhone);
    }

    /**
     * Test that invalid format throws exception.
     */
    public function test_invalid_format_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number format');

        PhoneNumber::fromString('abc123');
    }

    /**
     * Test that phone number trims whitespace.
     */
    public function test_phone_number_trims_whitespace(): void
    {
        $phoneNumber = PhoneNumber::fromString('  +84123456789  ');

        $this->assertEquals('+84123456789', (string) $phoneNumber);
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $phoneNumber1 = PhoneNumber::fromString('+84123456789');
        $phoneNumber2 = PhoneNumber::fromString('+84123456789');
        $phoneNumber3 = PhoneNumber::fromString('+84987654321');

        $this->assertTrue($phoneNumber1->equals($phoneNumber2));
        $this->assertFalse($phoneNumber1->equals($phoneNumber3));
    }

    /**
     * Test valid phone number formats.
     */
    public function test_valid_phone_number_formats(): void
    {
        $validPhones = ['+84123456789', '0123456789', '(012) 345-6789', '012-345-6789'];

        foreach ($validPhones as $validPhone) {
            $phoneNumber = PhoneNumber::fromString($validPhone);
            $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
        }
    }
}
