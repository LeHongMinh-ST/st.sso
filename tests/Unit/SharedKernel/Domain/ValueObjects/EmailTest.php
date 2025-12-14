<?php

declare(strict_types=1);

namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    /**
     * Test that valid email can be created.
     */
    public function test_valid_email_can_be_created(): void
    {
        $email = Email::fromString('test@example.com');

        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that email is normalized to lowercase.
     */
    public function test_email_is_normalized_to_lowercase(): void
    {
        $email = Email::fromString('TEST@EXAMPLE.COM');

        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that email trims whitespace.
     */
    public function test_email_trims_whitespace(): void
    {
        $email = Email::fromString('  test@example.com  ');

        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that invalid email throws exception.
     */
    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        Email::fromString('not-an-email');
    }

    /**
     * Test that empty email throws exception.
     */
    public function test_empty_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        Email::fromString('');
    }

    /**
     * Test that whitespace-only email throws exception.
     */
    public function test_whitespace_only_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        Email::fromString('   ');
    }

    /**
     * Test equals method.
     */
    public function test_equals_method(): void
    {
        $email1 = Email::fromString('test@example.com');
        $email2 = Email::fromString('test@example.com');
        $email3 = Email::fromString('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    /**
     * Test domain method.
     */
    public function test_domain_method(): void
    {
        $email = Email::fromString('test@example.com');

        $this->assertEquals('example.com', $email->domain());
    }

    /**
     * Test localPart method.
     */
    public function test_local_part_method(): void
    {
        $email = Email::fromString('test@example.com');

        $this->assertEquals('test', $email->localPart());
    }
}
