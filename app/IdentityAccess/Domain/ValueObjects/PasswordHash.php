<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Password Hash value object.
 * Represents a hashed password (never plain text).
 * Immutable and always valid.
 *
 * SECURITY: This class NEVER stores or accepts plain passwords.
 * Only hashed passwords are allowed.
 */
final class PasswordHash
{
    private string $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param string $value Hashed password (never plain text)
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * Get password hash value (for string casting).
     * SECURITY: Returns [REDACTED] to prevent accidental exposure.
     *
     * @return string
     */
    public function __toString(): string
    {
        return '[REDACTED]';
    }

    /**
     * Create PasswordHash from hashed string.
     *
     * SECURITY: This method only accepts already-hashed passwords.
     * Use PasswordHasher service to hash plain passwords.
     *
     * @param string $hashedPassword Already hashed password
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    /**
     * Create PasswordHash from plain text password using a hasher function.
     *
     * SECURITY: This method hashes the password immediately.
     * The plain password is never stored.
     *
     * @param string $plainPassword Plain text password
     * @param callable $hasher Password hasher function (e.g., Hash::make)
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromPlainText(string $plainPassword, callable $hasher): self
    {
        if (empty(trim($plainPassword))) {
            throw new InvalidArgumentException('Password cannot be empty');
        }

        if (mb_strlen($plainPassword) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }

        $hash = $hasher($plainPassword);

        return new self($hash);
    }

    /**
     * Get password hash value.
     *
     * SECURITY: This returns the hash, never the plain password.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Verify plain password against this hash.
     * Uses timing-safe comparison for security.
     *
     * @param string $plainPassword Plain text password to verify
     * @param callable $verifier Password verifier function (e.g., Hash::check)
     * @return bool True if password matches, false otherwise
     */
    public function verify(string $plainPassword, callable $verifier): bool
    {
        return $verifier($plainPassword, $this->value);
    }

    /**
     * Compare equality with another PasswordHash value object.
     *
     * @param PasswordHash $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        // Use timing-safe comparison for security
        return hash_equals($this->value, $other->value);
    }

    /**
     * Validate that the value is a valid password hash.
     *
     * SECURITY: This ensures we never accidentally store plain passwords.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Password hash cannot be empty');
        }

        // Laravel bcrypt hashes start with $2y$ and are 60 characters long
        // Argon2 hashes start with $argon2id$ and are longer
        // Basic validation: check if it looks like a hash (not too short, contains $)
        if (mb_strlen($value) < 20) {
            throw new InvalidArgumentException('Invalid password hash format');
        }

        // Additional validation: should contain $ (bcrypt/argon2 format)
        if (!str_contains($value, '$')) {
            throw new InvalidArgumentException('Invalid password hash format');
        }
    }
}
