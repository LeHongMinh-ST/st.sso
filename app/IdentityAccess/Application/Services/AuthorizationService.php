<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\Services;

use App\IdentityAccess\Application\UseCases\CheckPermissionUseCase;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Authorization service.
 * Centralizes permission checks using Use Cases from IdentityAccess Context.
 *
 * This service bridges between Eloquent User model (OrganizationalStructure)
 * and IdentityAccess Use Cases.
 */
final class AuthorizationService
{
    /**
     * @param CheckPermissionUseCase $checkPermissionUseCase
     */
    public function __construct(
        private readonly CheckPermissionUseCase $checkPermissionUseCase,
    ) {
    }

    /**
     * Check if user has permission.
     *
     * SECURITY: Default deny - returns false if user not found or has no roles.
     *
     * @param EloquentUser $user Eloquent User model
     * @param string $permissionCode Permission code to check
     * @return bool True if user has permission, false otherwise
     */
    public function can(EloquentUser $user, string $permissionCode): bool
    {
        try {
            $userRoleIds = $this->getUserRoleIds($user);

            if (empty($userRoleIds)) {
                return false;
            }

            return $this->checkPermissionUseCase->execute($userRoleIds, $permissionCode);
        } catch (Throwable $e) {
            // Log error but don't expose to caller
            \Illuminate\Support\Facades\Log::error('Authorization check failed', [
                'user_id' => $user->id,
                'permission' => $permissionCode,
                'error' => $e->getMessage(),
            ]);

            // Default deny on error
            return false;
        }
    }

    /**
     * Check if user has any of the permissions.
     *
     * SECURITY: Default deny - returns false if user not found or has no roles.
     *
     * @param EloquentUser $user Eloquent User model
     * @param array<string> $permissionCodes Permission codes to check
     * @return bool True if user has at least one permission, false otherwise
     */
    public function canAny(EloquentUser $user, array $permissionCodes): bool
    {
        foreach ($permissionCodes as $permissionCode) {
            if ($this->can($user, $permissionCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all permissions.
     *
     * SECURITY: Default deny - returns false if user not found or has no roles.
     *
     * @param EloquentUser $user Eloquent User model
     * @param array<string> $permissionCodes Permission codes to check
     * @return bool True if user has all permissions, false otherwise
     */
    public function canAll(EloquentUser $user, array $permissionCodes): bool
    {
        foreach ($permissionCodes as $permissionCode) {
            if (!$this->can($user, $permissionCode)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get user role IDs (UUIDs) from Eloquent User model.
     *
     * Bridge method to get role IDs from user's roles relationship.
     *
     * @param EloquentUser $user Eloquent User model
     * @return array<string> Array of Role ID UUIDs
     */
    private function getUserRoleIds(EloquentUser $user): array
    {
        $roleIds = [];

        // Get roles from user_roles relationship
        $roles = $user->roles;

        foreach ($roles as $role) {
            // Get UUID from role
            $hasUuidColumn = Schema::hasColumn('roles', 'uuid');

            if ($hasUuidColumn && null !== $role->uuid) {
                $roleIds[] = $role->uuid;
            } else {
                // Fallback: generate deterministic UUID from integer ID
                $roleIds[] = $this->generateDeterministicUuid('roles', $role->id);
            }
        }

        return $roleIds;
    }

    /**
     * Generate deterministic UUID from integer ID.
     * Temporary helper until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }
}
