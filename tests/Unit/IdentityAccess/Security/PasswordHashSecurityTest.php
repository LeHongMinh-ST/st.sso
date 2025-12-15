<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Security;

use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Security tests for PasswordHash Value Object.
 * Tests password security, hash validation, and information leakage prevention.
 */
final class PasswordHashSecurityTest extends TestCase
{
    /**
     * Test that __toString() returns [REDACTED] to prevent information leakage.
     */
    public function test_to_string_returns_redacted(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);

        $this->assertEquals('[REDACTED]', (string) $passwordHash);
        $this->assertNotEquals('password123', (string) $passwordHash);
    }

    /**
     * Test that toString() returns actual hash (for internal use only).
     */
    public function test_to_string_returns_actual_hash_for_internal_use(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromHash($hash);

        $this->assertEquals($hash, $passwordHash->toString());
        $this->assertNotEquals('password123', $passwordHash->toString());
    }

    /**
     * Test that password verification is timing-safe.
     */
    public function test_password_verification_is_timing_safe(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);

        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);

        // Test correct password
        $start = microtime(true);
        $result1 = $passwordHash->verify('password123', $verifier);
        $time1 = microtime(true) - $start;

        // Test incorrect password
        $start = microtime(true);
        $result2 = $passwordHash->verify('wrongpassword', $verifier);
        $time2 = microtime(true) - $start;

        $this->assertTrue($result1);
        $this->assertFalse($result2);

        // Timing should be similar (password_verify is timing-safe)
        // Note: This is a basic test, actual timing attacks require more sophisticated testing
        $this->assertLessThan(0.1, abs($time1 - $time2), 'Password verification should be timing-safe');
    }

    /**
     * Test that plain passwords are never stored.
     */
    public function test_plain_passwords_are_never_stored(): void
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwordHash = PasswordHash::fromPlainText('password123', $hasher);

        $hashValue = $passwordHash->toString();

        // Hash should not contain plain password
        $this->assertStringNotContainsString('password123', $hashValue);
        $this->assertStringStartsWith('$2y$', $hashValue); // Bcrypt format
    }

    /**
     * Test that weak passwords are rejected.
     */
    public function test_weak_passwords_are_rejected(): void
    {
        $weakPasswords = [
            '',
            '   ',
            'short',
            '1234567',
            'abcdefg',
        ];

        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        foreach ($weakPasswords as $weakPassword) {
            try {
                PasswordHash::fromPlainText($weakPassword, $hasher);
                $this->fail("Weak password should be rejected: {$weakPassword}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Password', $e->getMessage());
            }
        }
    }

    /**
     * Test that only valid hash formats are accepted.
     */
    public function test_only_valid_hash_formats_are_accepted(): void
    {
        $invalidHashes = [
            '',
            'tooshort',
            'plainpassword',
            'notavalidhashformat',
            '12345678901234567890', // Too short and no $
        ];

        foreach ($invalidHashes as $invalidHash) {
            try {
                PasswordHash::fromHash($invalidHash);
                $this->fail("Invalid hash should be rejected: {$invalidHash}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('hash', mb_strtolower($e->getMessage()));
            }
        }
    }

    /**
     * Test that valid hash formats are accepted.
     */
    public function test_valid_hash_formats_are_accepted(): void
    {
        $validBcryptHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $validArgon2Hash = '$argon2id$v=19$m=65536,t=4,p=3$c29tZXNhbHQ$RdescudvJCsgt3ub+b+dWRWJTmaaJObG';

        $bcryptHash = PasswordHash::fromHash($validBcryptHash);
        $argon2Hash = PasswordHash::fromHash($validArgon2Hash);

        $this->assertInstanceOf(PasswordHash::class, $bcryptHash);
        $this->assertInstanceOf(PasswordHash::class, $argon2Hash);
    }
}
