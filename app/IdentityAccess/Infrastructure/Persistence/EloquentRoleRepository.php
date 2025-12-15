<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Persistence;

use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\RoleId;
use App\IdentityAccess\Infrastructure\Eloquent\Role as EloquentRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of RoleRepositoryInterface.
 */
final class EloquentRoleRepository implements RoleRepositoryInterface
{
    /**
     * Generate a new Role ID.
     *
     * @return RoleId
     */
    public function nextIdentity(): RoleId
    {
        return RoleId::generate();
    }

    /**
     * Find Role by ID.
     *
     * @param RoleId $id Role ID
     * @return Role|null
     */
    public function findById(RoleId $id): ?Role
    {
        $hasUuidColumn = Schema::hasColumn('roles', 'uuid');

        $eloquentRole = null;
        if ($hasUuidColumn) {
            $eloquentRole = EloquentRole::where('uuid', $id->toString())->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('roles', $id->toString());
            if (null !== $integerId) {
                $eloquentRole = EloquentRole::find($integerId);
            }
        }

        if (null === $eloquentRole) {
            return null;
        }

        return $this->toDomain($eloquentRole);
    }

    /**
     * Find Role by name.
     *
     * @param string $name Role name
     * @return Role|null
     */
    public function findByName(string $name): ?Role
    {
        $eloquentRole = EloquentRole::where('name', $name)->first();

        if (null === $eloquentRole) {
            return null;
        }

        return $this->toDomain($eloquentRole);
    }

    /**
     * Find all roles.
     *
     * @return array<Role>
     */
    public function findAll(): array
    {
        $eloquentRoles = EloquentRole::all();

        return $eloquentRoles->map(fn ($role) => $this->toDomain($role))->toArray();
    }

    /**
     * Save Role aggregate.
     *
     * @param Role $role Role aggregate
     * @return void
     */
    public function save(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $hasUuidColumn = Schema::hasColumn('roles', 'uuid');

            $eloquentRole = null;
            if ($hasUuidColumn) {
                $eloquentRole = EloquentRole::where('uuid', $role->id()->toString())->first();
            }

            if (null === $eloquentRole) {
                $eloquentRole = new EloquentRole();
                if ($hasUuidColumn) {
                    $eloquentRole->uuid = $role->id()->toString();
                }
            }

            $eloquentRole->name = $role->name();
            $eloquentRole->display_name = $role->displayName();
            $eloquentRole->description = $role->description();

            $eloquentRole->save();

            // Sync permissions
            $permissionIds = $role->permissionIds();
            if (!empty($permissionIds)) {
                // Get integer IDs for permissions
                $integerPermissionIds = [];
                foreach ($permissionIds as $permissionUuid) {
                    $integerId = $this->getIntegerIdFromUuid('permissions', $permissionUuid);
                    if (null !== $integerId) {
                        $integerPermissionIds[] = $integerId;
                    }
                }
                $eloquentRole->permissions()->sync($integerPermissionIds);
            } else {
                $eloquentRole->permissions()->detach();
            }
        });
    }

    /**
     * Delete Role aggregate.
     *
     * @param RoleId $id Role ID
     * @return void
     */
    public function delete(RoleId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('roles', 'uuid');

        if ($hasUuidColumn) {
            EloquentRole::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('roles', $id->toString());
            if (null !== $integerId) {
                EloquentRole::destroy($integerId);
            }
        }
    }

    /**
     * Map Eloquent model to Domain aggregate.
     *
     * @param EloquentRole $eloquentRole Eloquent role model
     * @return Role Role aggregate
     */
    private function toDomain(EloquentRole $eloquentRole): Role
    {
        $hasUuidColumn = Schema::hasColumn('roles', 'uuid');

        $roleId = $hasUuidColumn && null !== $eloquentRole->uuid
            ? RoleId::fromString($eloquentRole->uuid)
            : RoleId::fromString($this->getUuidFromIntegerId('roles', $eloquentRole->id));

        // Get permission IDs (UUIDs)
        $permissionIds = [];
        foreach ($eloquentRole->permissions as $permission) {
            $permissionUuid = $hasUuidColumn && null !== $permission->uuid
                ? $permission->uuid
                : $this->getUuidFromIntegerId('permissions', $permission->id);
            $permissionIds[] = $permissionUuid;
        }

        // Use fromPersistence to reconstruct without triggering events
        return Role::fromPersistence(
            $roleId,
            $eloquentRole->name,
            $eloquentRole->display_name,
            $eloquentRole->description,
            $permissionIds,
        );
    }

    /**
     * Get UUID from integer ID.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function getUuidFromIntegerId(string $table, int $integerId): string
    {
        $hasUuidColumn = Schema::hasColumn($table, 'uuid');

        if ($hasUuidColumn) {
            $uuid = DB::table($table)->where('id', $integerId)->value('uuid');
            if (null !== $uuid) {
                return $uuid;
            }
        }

        return $this->generateDeterministicUuid($table, $integerId);
    }

    /**
     * Get integer ID from UUID.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int|null Integer ID or null if not found
     */
    private function getIntegerIdFromUuid(string $table, string $uuid): ?int
    {
        $hasUuidColumn = Schema::hasColumn($table, 'uuid');

        if ($hasUuidColumn) {
            return DB::table($table)->where('uuid', $uuid)->value('id');
        }

        return $this->getIntegerIdFromDeterministicUuid($table, $uuid);
    }

    /**
     * Generate deterministic UUID from integer ID.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = RamseyUuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return RamseyUuid::uuid5($namespace, $name)->toString();
    }

    /**
     * Get integer ID from deterministic UUID.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int|null Integer ID or null if not found
     */
    private function getIntegerIdFromDeterministicUuid(string $table, string $uuid): ?int
    {
        $records = DB::table($table)->select('id')->get();

        foreach ($records as $record) {
            $generatedUuid = $this->generateDeterministicUuid($table, $record->id);
            if ($generatedUuid === $uuid) {
                return $record->id;
            }
        }

        return null;
    }
}
