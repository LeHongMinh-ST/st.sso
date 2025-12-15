<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for changing password.
 *
 * SECURITY: Contains plain passwords temporarily during password change.
 */
final class ChangePasswordDTO
{
    /**
     * @param string $currentPassword Current plain text password
     * @param string $newPassword New plain text password
     */
    public function __construct(
        public readonly string $currentPassword,
        public readonly string $newPassword,
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
            $data['current_password'] ?? '',
            $data['new_password'] ?? '',
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
            $request->input('current_password', ''),
            $request->input('new_password', ''),
        );
    }

    /**
     * Convert DTO to array.
     *
     * SECURITY: Passwords are excluded from array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            // Passwords are intentionally excluded for security
        ];
    }
}
