<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Security;

use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Security tests for Username Value Object.
 * Tests SQL injection, XSS, and command injection prevention.
 */
final class UsernameSecurityTest extends TestCase
{
    /**
     * Test that SQL injection patterns are rejected.
     */
    public function test_sql_injection_patterns_are_rejected(): void
    {
        $sqlInjectionPatterns = [
            "' OR '1'='1",
            "admin'--",
            "admin'/*",
            "' UNION SELECT * FROM users--",
            "'; DROP TABLE users--",
            "' OR 1=1--",
            "' OR 'a'='a",
            "admin' OR '1'='1",
            "1' OR '1'='1",
            "' OR 1=1#",
        ];

        foreach ($sqlInjectionPatterns as $pattern) {
            try {
                Username::fromString($pattern);
                $this->fail("SQL injection pattern should be rejected: {$pattern}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Username', $e->getMessage());
            }
        }
    }

    /**
     * Test that XSS patterns are rejected.
     */
    public function test_xss_patterns_are_rejected(): void
    {
        $xssPatterns = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            'javascript:alert("XSS")',
            '<svg onload=alert("XSS")>',
            '"><script>alert("XSS")</script>',
        ];

        foreach ($xssPatterns as $pattern) {
            try {
                Username::fromString($pattern);
                $this->fail("XSS pattern should be rejected: {$pattern}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Username', $e->getMessage());
            }
        }
    }

    /**
     * Test that command injection patterns are rejected.
     */
    public function test_command_injection_patterns_are_rejected(): void
    {
        $commandInjectionPatterns = [
            '; ls -la',
            '| cat /etc/passwd',
            '&& rm -rf /',
            '`whoami`',
            '$(id)',
            '; rm -rf /',
        ];

        foreach ($commandInjectionPatterns as $pattern) {
            try {
                Username::fromString($pattern);
                $this->fail("Command injection pattern should be rejected: {$pattern}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Username', $e->getMessage());
            }
        }
    }

    /**
     * Test that path traversal patterns are rejected.
     */
    public function test_path_traversal_patterns_are_rejected(): void
    {
        $pathTraversalPatterns = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32',
            '../../etc/passwd',
            '....//....//etc/passwd',
        ];

        foreach ($pathTraversalPatterns as $pattern) {
            try {
                Username::fromString($pattern);
                $this->fail("Path traversal pattern should be rejected: {$pattern}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Username', $e->getMessage());
            }
        }
    }

    /**
     * Test that valid usernames are accepted.
     */
    public function test_valid_usernames_are_accepted(): void
    {
        $validUsernames = [
            'testuser',
            'test_user',
            'test-user',
            'test.user',
            'test123',
            'user123',
            'admin',
            'john.doe',
        ];

        foreach ($validUsernames as $username) {
            $vo = Username::fromString($username);
            $this->assertInstanceOf(Username::class, $vo);
            $this->assertEquals(mb_strtolower(trim($username)), (string) $vo);
        }
    }
}
