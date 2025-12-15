<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Persistence;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of UserRepositoryInterface.
 *
 * Note: Currently uses integer ID mapping. Will be updated after UUID migration.
 */
final class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Save a user aggregate.
     *
     * @param User $user User aggregate
     * @return void
     */
    public function save(User $user): void
    {
        DB::transaction(function () use ($user): void {
            // Check if UUID column exists (for future UUID migration)
            $hasUuidColumn = Schema::hasColumn('users', 'uuid');

            // Try to find by UUID if column exists, otherwise by integer ID
            $eloquentUser = null;
            if ($hasUuidColumn) {
                $eloquentUser = EloquentUser::where('uuid', $user->id()->toString())->first();
            }

            // If not found and UUID column doesn't exist, we'll create new
            // (In production, we'd need to map UUID to integer ID)
            if (null === $eloquentUser) {
                $eloquentUser = new EloquentUser();
                if ($hasUuidColumn) {
                    $eloquentUser->uuid = $user->id()->toString();
                }
            }

            // Map aggregate to Eloquent model
            $eloquentUser->user_name = $user->userName()->toString();
            $eloquentUser->first_name = $user->fullName()->firstName();
            $eloquentUser->last_name = $user->fullName()->lastName();
            $eloquentUser->email = (string) $user->email();
            $eloquentUser->code = $user->userCode()?->toString();
            $eloquentUser->phone = $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString();

            // Map FacultyId and DepartmentId
            // Use UUID columns if available, otherwise fallback to integer ID mapping
            if (null !== $user->facultyId()) {
                $hasFacultyUuidColumn = Schema::hasColumn('users', 'faculty_uuid');
                if ($hasFacultyUuidColumn) {
                    $eloquentUser->faculty_uuid = $user->facultyId()->toString();
                }
                // Also set integer ID for backward compatibility
                $eloquentUser->faculty_id = $this->getIntegerIdFromUuid('faculties', $user->facultyId()->toString());
            } else {
                $eloquentUser->faculty_id = null;
                if (Schema::hasColumn('users', 'faculty_uuid')) {
                    $eloquentUser->faculty_uuid = null;
                }
            }

            if (null !== $user->departmentId()) {
                $hasDepartmentUuidColumn = Schema::hasColumn('users', 'department_uuid');
                if ($hasDepartmentUuidColumn) {
                    $eloquentUser->department_uuid = $user->departmentId()->toString();
                }
                // Also set integer ID for backward compatibility
                $eloquentUser->department_id = $this->getIntegerIdFromUuid('departments', $user->departmentId()->toString());
            } else {
                $eloquentUser->department_id = null;
                if (Schema::hasColumn('users', 'department_uuid')) {
                    $eloquentUser->department_uuid = null;
                }
            }

            $eloquentUser->save();
        });
    }

    /**
     * Find user by ID.
     *
     * @param UserId $id User ID (UUID)
     * @return User|null
     */
    public function findById(UserId $id): ?User
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        $eloquentUser = null;
        if ($hasUuidColumn) {
            $eloquentUser = EloquentUser::where('uuid', $id->toString())->first();
        } else {
            // Temporary: Convert UUID to integer ID (for now, we'll need mapping)
            // This is a temporary solution until UUID migration is complete
            // In production, we'd use IdMappingService
            $integerId = $this->getIntegerIdFromUuid('users', $id->toString());
            if (null !== $integerId) {
                $eloquentUser = EloquentUser::find($integerId);
            }
        }

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find user by email.
     *
     * @param Email $email Email address
     * @return User|null
     */
    public function findByEmail(Email $email): ?User
    {
        $eloquentUser = EloquentUser::where('email', (string) $email)->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find user by username.
     *
     * @param UserName $userName Username
     * @return User|null
     */
    public function findByUserName(UserName $userName): ?User
    {
        $eloquentUser = EloquentUser::where('user_name', $userName->toString())->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find user by user code.
     *
     * @param UserCode $userCode User code
     * @return User|null
     */
    public function findByUserCode(UserCode $userCode): ?User
    {
        $eloquentUser = EloquentUser::where('code', $userCode->toString())->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find users by faculty ID.
     *
     * @param FacultyId $facultyId Faculty ID (UUID)
     * @return array<User>
     */
    public function findByFacultyId(FacultyId $facultyId): array
    {
        $hasFacultyUuidColumn = Schema::hasColumn('users', 'faculty_uuid');

        $eloquentUsers = null;
        if ($hasFacultyUuidColumn) {
            $eloquentUsers = EloquentUser::where('faculty_uuid', $facultyId->toString())->get();
        } else {
            $integerId = $this->getIntegerIdFromUuid('faculties', $facultyId->toString());
            if (null === $integerId) {
                return [];
            }
            $eloquentUsers = EloquentUser::where('faculty_id', $integerId)->get();
        }

        return $eloquentUsers->map(fn ($user) => $this->toDomain($user))->toArray();
    }

    /**
     * Find users by department ID.
     *
     * @param DepartmentId $departmentId Department ID (UUID)
     * @return array<User>
     */
    public function findByDepartmentId(DepartmentId $departmentId): array
    {
        $hasDepartmentUuidColumn = Schema::hasColumn('users', 'department_uuid');

        $eloquentUsers = null;
        if ($hasDepartmentUuidColumn) {
            $eloquentUsers = EloquentUser::where('department_uuid', $departmentId->toString())->get();
        } else {
            $integerId = $this->getIntegerIdFromUuid('departments', $departmentId->toString());
            if (null === $integerId) {
                return [];
            }
            $eloquentUsers = EloquentUser::where('department_id', $integerId)->get();
        }

        return $eloquentUsers->map(fn ($user) => $this->toDomain($user))->toArray();
    }

    /**
     * Delete user by ID.
     *
     * @param UserId $id User ID (UUID)
     * @return void
     */
    public function delete(UserId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn) {
            EloquentUser::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('users', $id->toString());
            if (null !== $integerId) {
                EloquentUser::destroy($integerId);
            }
        }
    }

    /**
     * Check if user exists by email.
     *
     * @param Email $email Email address
     * @return bool
     */
    public function existsByEmail(Email $email): bool
    {
        return EloquentUser::where('email', (string) $email)->exists();
    }

    /**
     * Check if user exists by username.
     *
     * @param UserName $userName Username
     * @return bool
     */
    public function existsByUserName(UserName $userName): bool
    {
        return EloquentUser::where('user_name', $userName->toString())->exists();
    }

    /**
     * Check if user exists by user code.
     *
     * @param UserCode $userCode User code
     * @return bool
     */
    public function existsByUserCode(UserCode $userCode): bool
    {
        return EloquentUser::where('code', $userCode->toString())->exists();
    }

    /**
     * Map Eloquent model to Domain aggregate.
     *
     * @param EloquentUser $eloquentUser Eloquent user model
     * @return User User aggregate
     */
    private function toDomain(EloquentUser $eloquentUser): User
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        // Get UUID: use uuid column if exists, otherwise generate from integer ID
        $userId = $hasUuidColumn && null !== $eloquentUser->uuid
            ? UserId::fromString($eloquentUser->uuid)
            : UserId::fromString($this->getUuidFromIntegerId('users', $eloquentUser->id));

        $userName = UserName::fromString($eloquentUser->user_name);
        $fullName = FullName::fromParts($eloquentUser->first_name, $eloquentUser->last_name);
        $email = Email::fromString($eloquentUser->email);
        $userCode = $eloquentUser->code ? UserCode::fromString($eloquentUser->code) : null;
        $phoneNumber = $eloquentUser->phone ? PhoneNumber::fromString($eloquentUser->phone) : null;

        // Map FacultyId and DepartmentId
        // Use UUID columns if available, otherwise fallback to integer ID mapping
        $facultyId = null;
        if (Schema::hasColumn('users', 'faculty_uuid') && null !== $eloquentUser->faculty_uuid) {
            $facultyId = FacultyId::fromString($eloquentUser->faculty_uuid);
        } elseif (null !== $eloquentUser->faculty_id) {
            $facultyUuid = $this->getUuidFromIntegerId('faculties', $eloquentUser->faculty_id);
            $facultyId = FacultyId::fromString($facultyUuid);
        }

        $departmentId = null;
        if (Schema::hasColumn('users', 'department_uuid') && null !== $eloquentUser->department_uuid) {
            $departmentId = DepartmentId::fromString($eloquentUser->department_uuid);
        } elseif (null !== $eloquentUser->department_id) {
            $departmentUuid = $this->getUuidFromIntegerId('departments', $eloquentUser->department_id);
            $departmentId = DepartmentId::fromString($departmentUuid);
        }

        // Use fromPersistence to reconstruct without triggering events
        return User::fromPersistence(
            $userId,
            $userName,
            $fullName,
            $email,
            $userCode,
            $phoneNumber,
            $facultyId,
            $departmentId,
        );
    }

    /**
     * Get UUID from integer ID.
     * Temporary helper method until UUID migration is complete.
     * After UUID migration, this will use the uuid column directly.
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

        // Temporary: Generate deterministic UUID from integer ID
        // This is a workaround until UUID migration is complete
        // In production, UUIDs should be stored in database
        return $this->generateDeterministicUuid($table, $integerId);
    }

    /**
     * Get integer ID from UUID.
     * Temporary helper method until UUID migration is complete.
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

        // Temporary: Try to find by deterministic UUID
        // This is a workaround until UUID migration is complete
        // In production, UUIDs should be stored in database
        return $this->getIntegerIdFromDeterministicUuid($table, $uuid);
    }

    /**
     * Generate deterministic UUID from integer ID.
     * This is a temporary workaround until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        // Use namespace UUID v5 to generate deterministic UUID
        // This ensures same integer ID always generates same UUID
        $namespace = RamseyUuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8'); // DNS namespace
        $name = "{$table}:{$integerId}";

        return RamseyUuid::uuid5($namespace, $name)->toString();
    }

    /**
     * Get integer ID from deterministic UUID.
     * This is a temporary workaround until UUID migration is complete.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int|null Integer ID or null if not found
     */
    private function getIntegerIdFromDeterministicUuid(string $table, string $uuid): ?int
    {
        // Try to find integer ID by checking all records
        // This is inefficient but temporary until UUID migration
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
