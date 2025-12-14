<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Persistence;

use App\Models\Department as EloquentDepartment;
use App\OrganizationalStructure\Domain\Entities\Department;
use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of DepartmentRepositoryInterface.
 *
 * Note: Currently uses integer ID mapping. Will be updated after UUID migration.
 */
final class EloquentDepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * Save a department entity.
     *
     * @param Department $department Department entity
     * @return void
     */
    public function save(Department $department): void
    {
        DB::transaction(function () use ($department): void {
            $hasUuidColumn = Schema::hasColumn('departments', 'uuid');

            $eloquentDepartment = null;
            if ($hasUuidColumn) {
                $eloquentDepartment = EloquentDepartment::where('uuid', $department->id()->toString())->first();
            }

            if (null === $eloquentDepartment) {
                $eloquentDepartment = new EloquentDepartment();
                if ($hasUuidColumn) {
                    $eloquentDepartment->uuid = $department->id()->toString();
                }
            }

            $eloquentDepartment->name = $department->name();
            $eloquentDepartment->status = $department->status();

            // Map FacultyId to integer ID
            $facultyIntegerId = $this->getIntegerIdFromUuid('faculties', $department->facultyId()->toString());
            $eloquentDepartment->faculty_id = $facultyIntegerId;

            $eloquentDepartment->save();
        });
    }

    /**
     * Find department by ID.
     *
     * @param DepartmentId $id Department ID (UUID)
     * @return Department|null
     */
    public function findById(DepartmentId $id): ?Department
    {
        $hasUuidColumn = Schema::hasColumn('departments', 'uuid');

        $eloquentDepartment = null;
        if ($hasUuidColumn) {
            $eloquentDepartment = EloquentDepartment::where('uuid', $id->toString())->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('departments', $id->toString());
            if (null !== $integerId) {
                $eloquentDepartment = EloquentDepartment::find($integerId);
            }
        }

        if (null === $eloquentDepartment) {
            return null;
        }

        return $this->toDomain($eloquentDepartment);
    }

    /**
     * Find department by name.
     *
     * @param string $name Department name
     * @return Department|null
     */
    public function findByName(string $name): ?Department
    {
        $eloquentDepartment = EloquentDepartment::where('name', $name)->first();

        if (null === $eloquentDepartment) {
            return null;
        }

        return $this->toDomain($eloquentDepartment);
    }

    /**
     * Find departments by faculty ID.
     *
     * @param FacultyId $facultyId Faculty ID (UUID)
     * @return array<Department>
     */
    public function findByFacultyId(FacultyId $facultyId): array
    {
        $integerId = $this->getIntegerIdFromUuid('faculties', $facultyId->toString());
        if (null === $integerId) {
            return [];
        }

        $eloquentDepartments = EloquentDepartment::where('faculty_id', $integerId)->get();

        return $eloquentDepartments->map(fn ($department) => $this->toDomain($department))->toArray();
    }

    /**
     * Find all departments.
     *
     * @return array<Department>
     */
    public function findAll(): array
    {
        $eloquentDepartments = EloquentDepartment::all();

        return $eloquentDepartments->map(fn ($department) => $this->toDomain($department))->toArray();
    }

    /**
     * Delete department by ID.
     *
     * @param DepartmentId $id Department ID (UUID)
     * @return void
     */
    public function delete(DepartmentId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('departments', 'uuid');

        if ($hasUuidColumn) {
            EloquentDepartment::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('departments', $id->toString());
            if (null !== $integerId) {
                EloquentDepartment::destroy($integerId);
            }
        }
    }

    /**
     * Check if department exists by name.
     *
     * @param string $name Department name
     * @return bool
     */
    public function existsByName(string $name): bool
    {
        return EloquentDepartment::where('name', $name)->exists();
    }

    /**
     * Map Eloquent model to Domain entity.
     *
     * @param EloquentDepartment $eloquentDepartment Eloquent department model
     * @return Department Department entity
     */
    private function toDomain(EloquentDepartment $eloquentDepartment): Department
    {
        $hasUuidColumn = Schema::hasColumn('departments', 'uuid');

        $departmentId = $hasUuidColumn && null !== $eloquentDepartment->uuid
            ? DepartmentId::fromString($eloquentDepartment->uuid)
            : DepartmentId::fromString($this->getUuidFromIntegerId('departments', $eloquentDepartment->id));

        // Map FacultyId
        $facultyUuid = $this->getUuidFromIntegerId('faculties', $eloquentDepartment->faculty_id);
        $facultyId = FacultyId::fromString($facultyUuid);

        // Use fromPersistence to reconstruct without triggering events
        return Department::fromPersistence(
            $departmentId,
            $eloquentDepartment->name,
            $eloquentDepartment->status,
            $facultyId,
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
