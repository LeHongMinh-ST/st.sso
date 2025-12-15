<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Persistence;

use App\IdentityAccess\Domain\Aggregates\Permission;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\PermissionId;
use App\IdentityAccess\Infrastructure\Eloquent\Permission as EloquentPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of PermissionRepositoryInterface.
 */
final class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Generate a new Permission ID.
     *
     * @return PermissionId
     */
    public function nextIdentity(): PermissionId
    {
        return PermissionId::generate();
    }

    /**
     * Find Permission by ID.
     *
     * @param PermissionId $id Permission ID
     * @return Permission|null
     */
    public function findById(PermissionId $id): ?Permission
    {
        $hasUuidColumn = Schema::hasColumn('permissions', 'uuid');

        $eloquentPermission = null;
        if ($hasUuidColumn) {
            $eloquentPermission = EloquentPermission::where('uuid', $id->toString())->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('permissions', $id->toString());
            if (null !== $integerId) {
                $eloquentPermission = EloquentPermission::find($integerId);
            }
        }

        if (null === $eloquentPermission) {
            return null;
        }

        return $this->toDomain($eloquentPermission);
    }

    /**
     * Find Permission by code.
     *
     * @param string $code Permission code
     * @return Permission|null
     */
    public function findByCode(string $code): ?Permission
    {
        $eloquentPermission = EloquentPermission::where('code', $code)->first();

        if (null === $eloquentPermission) {
            return null;
        }

        return $this->toDomain($eloquentPermission);
    }

    /**
     * Find all permissions.
     *
     * @return array<Permission>
     */
    public function findAll(): array
    {
        $eloquentPermissions = EloquentPermission::all();

        return $eloquentPermissions->map(fn ($permission) => $this->toDomain($permission))->toArray();
    }

    /**
     * Find permissions by group ID.
     *
     * @param string $permissionGroupId Permission group ID
     * @return array<Permission>
     */
    public function findByGroupId(string $permissionGroupId): array
    {
        $hasUuidColumn = Schema::hasColumn('permission_groups', 'uuid');

        $integerGroupId = null;
        if ($hasUuidColumn) {
            $integerGroupId = DB::table('permission_groups')->where('uuid', $permissionGroupId)->value('id');
        } else {
            $integerGroupId = $this->getIntegerIdFromUuid('permission_groups', $permissionGroupId);
        }

        if (null === $integerGroupId) {
            return [];
        }

        $eloquentPermissions = EloquentPermission::where('permission_group_id', $integerGroupId)->get();

        return $eloquentPermissions->map(fn ($permission) => $this->toDomain($permission))->toArray();
    }

    /**
     * Save Permission aggregate.
     *
     * @param Permission $permission Permission aggregate
     * @return void
     */
    public function save(Permission $permission): void
    {
        DB::transaction(function () use ($permission): void {
            $hasUuidColumn = Schema::hasColumn('permissions', 'uuid');

            $eloquentPermission = null;
            if ($hasUuidColumn) {
                $eloquentPermission = EloquentPermission::where('uuid', $permission->id()->toString())->first();
            }

            if (null === $eloquentPermission) {
                $eloquentPermission = new EloquentPermission();
                if ($hasUuidColumn) {
                    $eloquentPermission->uuid = $permission->id()->toString();
                }
            }

            $eloquentPermission->name = $permission->name();
            $eloquentPermission->code = $permission->code();
            $eloquentPermission->display_name = $permission->displayName();
            $eloquentPermission->description = $permission->description();

            // Map permission group ID
            if (null !== $permission->permissionGroupId()) {
                $hasGroupUuidColumn = Schema::hasColumn('permission_groups', 'uuid');
                if ($hasGroupUuidColumn) {
                    $integerGroupId = DB::table('permission_groups')
                        ->where('uuid', $permission->permissionGroupId())
                        ->value('id');
                    $eloquentPermission->permission_group_id = $integerGroupId;
                } else {
                    $integerGroupId = $this->getIntegerIdFromUuid('permission_groups', $permission->permissionGroupId());
                    $eloquentPermission->permission_group_id = $integerGroupId;
                }
            } else {
                $eloquentPermission->permission_group_id = null;
            }

            $eloquentPermission->save();
        });
    }

    /**
     * Delete Permission aggregate.
     *
     * @param PermissionId $id Permission ID
     * @return void
     */
    public function delete(PermissionId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('permissions', 'uuid');

        if ($hasUuidColumn) {
            EloquentPermission::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('permissions', $id->toString());
            if (null !== $integerId) {
                EloquentPermission::destroy($integerId);
            }
        }
    }

    /**
     * Map Eloquent model to Domain aggregate.
     *
     * @param EloquentPermission $eloquentPermission Eloquent permission model
     * @return Permission Permission aggregate
     */
    private function toDomain(EloquentPermission $eloquentPermission): Permission
    {
        $hasUuidColumn = Schema::hasColumn('permissions', 'uuid');

        $permissionId = $hasUuidColumn && null !== $eloquentPermission->uuid
            ? PermissionId::fromString($eloquentPermission->uuid)
            : PermissionId::fromString($this->getUuidFromIntegerId('permissions', $eloquentPermission->id));

        // Map permission group ID
        $permissionGroupId = null;
        if (null !== $eloquentPermission->permission_group_id) {
            $hasGroupUuidColumn = Schema::hasColumn('permission_groups', 'uuid');
            if ($hasGroupUuidColumn) {
                $groupUuid = DB::table('permission_groups')
                    ->where('id', $eloquentPermission->permission_group_id)
                    ->value('uuid');
                $permissionGroupId = $groupUuid;
            } else {
                $permissionGroupId = $this->getUuidFromIntegerId('permission_groups', $eloquentPermission->permission_group_id);
            }
        }

        // Use fromPersistence to reconstruct without triggering events
        return Permission::fromPersistence(
            $permissionId,
            $eloquentPermission->name,
            $eloquentPermission->code,
            $eloquentPermission->display_name,
            $eloquentPermission->description,
            $permissionGroupId,
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
