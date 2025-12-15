<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for assigning permission to role.
 */
final class AssignPermissionToRoleDTO
{
    /**
     * @param string $roleId Role ID (UUID)
     * @param string $permissionId Permission ID (UUID)
     */
    public function __construct(
        public readonly string $roleId,
        public readonly string $permissionId,
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
            $data['role_id'] ?? '',
            $data['permission_id'] ?? '',
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
            $request->input('role_id', ''),
            $request->input('permission_id', ''),
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'role_id' => $this->roleId,
            'permission_id' => $this->permissionId,
        ];
    }
}
