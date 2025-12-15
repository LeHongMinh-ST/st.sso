<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Persistence;

use App\OrganizationalStructure\Domain\Entities\Faculty;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty as EloquentFaculty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of FacultyRepositoryInterface.
 *
 * Note: Currently uses integer ID mapping. Will be updated after UUID migration.
 */
final class EloquentFacultyRepository implements FacultyRepositoryInterface
{
    /**
     * Save a faculty entity.
     *
     * @param Faculty $faculty Faculty entity
     * @return void
     */
    public function save(Faculty $faculty): void
    {
        DB::transaction(function () use ($faculty): void {
            $hasUuidColumn = Schema::hasColumn('faculties', 'uuid');

            $eloquentFaculty = null;
            if ($hasUuidColumn) {
                $eloquentFaculty = EloquentFaculty::where('uuid', $faculty->id()->toString())->first();
            }

            if (null === $eloquentFaculty) {
                $eloquentFaculty = new EloquentFaculty();
                if ($hasUuidColumn) {
                    $eloquentFaculty->uuid = $faculty->id()->toString();
                }
            }

            $eloquentFaculty->name = $faculty->name();
            $eloquentFaculty->status = $faculty->status();
            $eloquentFaculty->description = $faculty->description();

            $eloquentFaculty->save();
        });
    }

    /**
     * Find faculty by ID.
     *
     * @param FacultyId $id Faculty ID (UUID)
     * @return Faculty|null
     */
    public function findById(FacultyId $id): ?Faculty
    {
        $hasUuidColumn = Schema::hasColumn('faculties', 'uuid');

        $eloquentFaculty = null;
        if ($hasUuidColumn) {
            $eloquentFaculty = EloquentFaculty::where('uuid', $id->toString())->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('faculties', $id->toString());
            if (null !== $integerId) {
                $eloquentFaculty = EloquentFaculty::find($integerId);
            }
        }

        if (null === $eloquentFaculty) {
            return null;
        }

        return $this->toDomain($eloquentFaculty);
    }

    /**
     * Find faculty by name.
     *
     * @param string $name Faculty name
     * @return Faculty|null
     */
    public function findByName(string $name): ?Faculty
    {
        $eloquentFaculty = EloquentFaculty::where('name', $name)->first();

        if (null === $eloquentFaculty) {
            return null;
        }

        return $this->toDomain($eloquentFaculty);
    }

    /**
     * Find all faculties.
     *
     * @return array<Faculty>
     */
    public function findAll(): array
    {
        $eloquentFaculties = EloquentFaculty::all();

        return $eloquentFaculties->map(fn ($faculty) => $this->toDomain($faculty))->toArray();
    }

    /**
     * Delete faculty by ID.
     *
     * @param FacultyId $id Faculty ID (UUID)
     * @return void
     */
    public function delete(FacultyId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('faculties', 'uuid');

        if ($hasUuidColumn) {
            EloquentFaculty::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('faculties', $id->toString());
            if (null !== $integerId) {
                EloquentFaculty::destroy($integerId);
            }
        }
    }

    /**
     * Check if faculty exists by name.
     *
     * @param string $name Faculty name
     * @return bool
     */
    public function existsByName(string $name): bool
    {
        return EloquentFaculty::where('name', $name)->exists();
    }

    /**
     * Map Eloquent model to Domain entity.
     *
     * @param EloquentFaculty $eloquentFaculty Eloquent faculty model
     * @return Faculty Faculty entity
     */
    private function toDomain(EloquentFaculty $eloquentFaculty): Faculty
    {
        $hasUuidColumn = Schema::hasColumn('faculties', 'uuid');

        $facultyId = $hasUuidColumn && null !== $eloquentFaculty->uuid
            ? FacultyId::fromString($eloquentFaculty->uuid)
            : FacultyId::fromString($this->getUuidFromIntegerId('faculties', $eloquentFaculty->id));

        // Use fromPersistence to reconstruct without triggering events
        return Faculty::fromPersistence(
            $facultyId,
            $eloquentFaculty->name,
            $eloquentFaculty->status,
            $eloquentFaculty->description,
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
