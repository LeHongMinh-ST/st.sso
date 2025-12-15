<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for authenticating a user.
 *
 * SECURITY: This DTO contains plain password temporarily during authentication.
 * Password should be cleared from memory after use.
 */
final class AuthenticateUserDTO
{
    /**
     * @param string $username Username or email for authentication
     * @param string $password Plain text password (will be verified, never stored)
     */
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['username'] ?? '',
            $data['password'] ?? '',
        );
    }

    /**
     * Create DTO from Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->input('username', ''),
            $request->input('password', ''),
        );
    }

    /**
     * Convert DTO to array.
     *
     * SECURITY: Password is excluded from array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            // Password is intentionally excluded for security
        ];
    }
}
