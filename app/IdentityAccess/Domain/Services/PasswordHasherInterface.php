<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Services;

use App\IdentityAccess\Domain\ValueObjects\PasswordHash;

/**
 * Password Hasher Interface.
 * Defines contract for hashing and verifying passwords.
 *
 * SECURITY: This interface ensures proper password handling:
 * - Strong hashing algorithm (bcrypt cost >= 10 or argon2id)
 * - Timing-safe password verification
 * - Never stores plain passwords
 */
interface PasswordHasherInterface
{
    /**
     * Hash a plain text password.
     *
     * SECURITY: Uses strong hashing algorithm (bcrypt cost >= 10 or argon2id).
     *
     * @param string $plainPassword Plain text password
     * @return PasswordHash Hashed password (never plain text)
     */
    public function hash(string $plainPassword): PasswordHash;

    /**
     * Verify a plain password against a hash.
     *
     * SECURITY: Uses timing-safe comparison to prevent timing attacks.
     *
     * @param string $plainPassword Plain text password to verify
     * @param PasswordHash $hash Password hash to verify against
     * @return bool True if password matches, false otherwise
     */
    public function verify(string $plainPassword, PasswordHash $hash): bool;
}
