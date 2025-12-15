<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Domain\ValueObjects;

use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PasswordHashTest extends TestCase
{
    /**
     * Test that password hash can be created from plain text.
     */
    public function test_can_create_from_plain_text(): void
    {
        $hasher = fn (string $password): string => password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertEquals('[REDACTED]', (string) $passwordHash);
    }

    /**
     * Test that password hash can be created from existing hash.
     */
    public function test_can_create_from_hash(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromHash($hash);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertEquals($hash, $passwordHash->toString());
    }

    /**
     * Test that empty password throws exception.
     */
    public function test_empty_password_throws_exception(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty');

        PasswordHash::fromPlainText('', $hasher);
    }

    /**
     * Test that whitespace-only password throws exception.
     */
    public function test_whitespace_only_password_throws_exception(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty');

        PasswordHash::fromPlainText('   ', $hasher);
    }

    /**
     * Test that short password throws exception.
     */
    public function test_short_password_throws_exception(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters');

        PasswordHash::fromPlainText('short', $hasher);
    }

    /**
     * Test that password with 7 characters throws exception.
     */
    public function test_password_with_7_characters_throws_exception(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters');

        PasswordHash::fromPlainText('1234567', $hasher);
    }

    /**
     * Test that password with 8 characters is valid.
     */
    public function test_password_with_8_characters_is_valid(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromPlainText('12345678', $hasher);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
    }

    /**
     * Test that verify returns true for correct password.
     */
    public function test_verify_returns_true_for_correct_password(): void
    {
        $plainPassword = 'password123';
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);

        $passwordHash = PasswordHash::fromPlainText($plainPassword, $hasher);
        $result = $passwordHash->verify($plainPassword, $verifier);

        $this->assertTrue($result);
    }

    /**
     * Test that verify returns false for incorrect password.
     */
    public function test_verify_returns_false_for_incorrect_password(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);

        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);
        $result = $passwordHash->verify('wrongpassword', $verifier);

        $this->assertFalse($result);
    }

    /**
     * Test that __toString() returns [REDACTED] for security.
     */
    public function test_to_string_returns_redacted(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);

        $this->assertEquals('[REDACTED]', (string) $passwordHash);
    }

    /**
     * Test that toString() returns actual hash (for internal use).
     */
    public function test_to_string_returns_actual_hash(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromHash($hash);

        $this->assertEquals($hash, $passwordHash->toString());
    }

    /**
     * Test that equals method works correctly.
     */
    public function test_equals_method(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        $passwordHash1 = PasswordHash::fromHash($hash);
        $passwordHash2 = PasswordHash::fromHash($hash);
        $passwordHash3 = PasswordHash::fromPlainText('otherpassword', $hasher);

        $this->assertTrue($passwordHash1->equals($passwordHash2));
        $this->assertFalse($passwordHash1->equals($passwordHash3));
    }

    /**
     * Test that invalid hash format throws exception.
     */
    public function test_invalid_hash_format_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid password hash format');

        PasswordHash::fromHash('tooshort');
    }

    /**
     * Test that hash without $ throws exception.
     */
    public function test_hash_without_dollar_sign_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid password hash format');

        PasswordHash::fromHash('thisisnotavalidhashformatbecauseitdoesnthaveadollarsign');
    }

    /**
     * Test that empty hash throws exception.
     */
    public function test_empty_hash_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password hash cannot be empty');

        PasswordHash::fromHash('');
    }

    /**
     * Test that valid bcrypt hash is accepted.
     */
    public function test_valid_bcrypt_hash_is_accepted(): void
    {
        $bcryptHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $passwordHash = PasswordHash::fromHash($bcryptHash);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
    }

    /**
     * Test that valid argon2 hash is accepted.
     */
    public function test_valid_argon2_hash_is_accepted(): void
    {
        $argon2Hash = '$argon2id$v=19$m=65536,t=4,p=3$c29tZXNhbHQ$RdescudvJCsgt3ub+b+dWRWJTmaaJObG';
        $passwordHash = PasswordHash::fromHash($argon2Hash);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
    }
}
