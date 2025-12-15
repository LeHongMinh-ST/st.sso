<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Services;

use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use Illuminate\Support\Facades\Hash;

/**
 * Laravel password hasher implementation.
 *
 * SECURITY: Uses Laravel's Hash facade which uses bcrypt (cost >= 10) by default.
 * Provides timing-safe password verification.
 */
final class LaravelPasswordHasher implements PasswordHasherInterface
{
    /**
     * Hash a plain text password.
     *
     * SECURITY: Uses Laravel's Hash::make() which uses bcrypt with cost >= 10.
     *
     * @param string $plainPassword Plain text password
     * @return PasswordHash Hashed password (never plain text)
     */
    public function hash(string $plainPassword): PasswordHash
    {
        return PasswordHash::fromPlainText($plainPassword, fn (string $p) => Hash::make($p));
    }

    /**
     * Verify a plain password against a hash.
     *
     * SECURITY: Uses Hash::check() which provides timing-safe comparison.
     *
     * @param string $plainPassword Plain text password to verify
     * @param PasswordHash $hash Password hash to verify against
     * @return bool True if password matches, false otherwise
     */
    public function verify(string $plainPassword, PasswordHash $hash): bool
    {
        return Hash::check($plainPassword, $hash->toString());
    }
}
