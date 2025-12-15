<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Persistence;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\IdentityAccess\Infrastructure\Eloquent\UserIdentity as EloquentUserIdentity;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Eloquent implementation of UserIdentityRepositoryInterface.
 *
 * Note: UserIdentity shares the same table (users) with OrganizationalStructure User.
 * This repository handles authentication-related fields only.
 */
final class EloquentUserIdentityRepository implements UserIdentityRepositoryInterface
{
    /**
     * Generate a new UserIdentity ID.
     *
     * @return UserIdentityId
     */
    public function nextIdentity(): UserIdentityId
    {
        return UserIdentityId::generate();
    }

    /**
     * Find UserIdentity by ID.
     *
     * @param UserIdentityId $id User Identity ID
     * @return UserIdentity|null
     */
    public function findById(UserIdentityId $id): ?UserIdentity
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        $eloquentUser = null;
        if ($hasUuidColumn) {
            $eloquentUser = EloquentUserIdentity::where('uuid', $id->toString())->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('users', $id->toString());
            if (null !== $integerId) {
                $eloquentUser = EloquentUserIdentity::find($integerId);
            }
        }

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find UserIdentity by username.
     *
     * @param Username $username Username
     * @return UserIdentity|null
     */
    public function findByUsername(Username $username): ?UserIdentity
    {
        $eloquentUser = EloquentUserIdentity::where('user_name', $username->toString())->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find UserIdentity by email.
     *
     * @param Email $email Email address
     * @return UserIdentity|null
     */
    public function findByEmail(Email $email): ?UserIdentity
    {
        $eloquentUser = EloquentUserIdentity::where('email', (string) $email)->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Find UserIdentity by OrganizationalStructure User ID.
     *
     * @param string $organizationalStructureUserId User ID from OrganizationalStructure context
     * @return UserIdentity|null
     */
    public function findByOrganizationalStructureUserId(string $organizationalStructureUserId): ?UserIdentity
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        $eloquentUser = null;
        if ($hasUuidColumn) {
            $eloquentUser = EloquentUserIdentity::where('uuid', $organizationalStructureUserId)->first();
        } else {
            $integerId = $this->getIntegerIdFromUuid('users', $organizationalStructureUserId);
            if (null !== $integerId) {
                $eloquentUser = EloquentUserIdentity::find($integerId);
            }
        }

        if (null === $eloquentUser) {
            return null;
        }

        return $this->toDomain($eloquentUser);
    }

    /**
     * Save UserIdentity aggregate.
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @return void
     */
    public function save(UserIdentity $userIdentity): void
    {
        DB::transaction(function () use ($userIdentity): void {
            $hasUuidColumn = Schema::hasColumn('users', 'uuid');

            // Try to find by UUID if column exists, otherwise by integer ID
            $eloquentUser = null;
            if ($hasUuidColumn) {
                $eloquentUser = EloquentUserIdentity::where('uuid', $userIdentity->id()->toString())->first();
            }

            if (null === $eloquentUser) {
                $eloquentUser = new EloquentUserIdentity();
                if ($hasUuidColumn) {
                    $eloquentUser->uuid = $userIdentity->id()->toString();
                }
            }

            // Map aggregate to Eloquent model (authentication fields only)
            $eloquentUser->user_name = $userIdentity->username()->toString();
            $eloquentUser->email = (string) $userIdentity->email();
            $eloquentUser->password = $userIdentity->passwordHash()->toString();
            $eloquentUser->is_change_password = $userIdentity->mustChangePassword();
            $eloquentUser->is_only_login_ms = $userIdentity->isOnlyMicrosoftLogin();

            // Note: status field is managed by OrganizationalStructure context
            // We only handle authentication-related fields here

            $eloquentUser->save();
        });
    }

    /**
     * Delete UserIdentity aggregate.
     *
     * @param UserIdentityId $id User Identity ID
     * @return void
     */
    public function delete(UserIdentityId $id): void
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn) {
            EloquentUserIdentity::where('uuid', $id->toString())->delete();
        } else {
            $integerId = $this->getIntegerIdFromUuid('users', $id->toString());
            if (null !== $integerId) {
                EloquentUserIdentity::destroy($integerId);
            }
        }
    }

    /**
     * Map Eloquent model to Domain aggregate.
     *
     * @param EloquentUserIdentity $eloquentUser Eloquent user identity model
     * @return UserIdentity User Identity aggregate
     */
    private function toDomain(EloquentUserIdentity $eloquentUser): UserIdentity
    {
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        // Get UUID: use uuid column if exists, otherwise generate from integer ID
        $userIdentityId = $hasUuidColumn && null !== $eloquentUser->uuid
            ? UserIdentityId::fromString($eloquentUser->uuid)
            : UserIdentityId::fromString($this->getUuidFromIntegerId('users', $eloquentUser->id));

        $username = Username::fromString($eloquentUser->user_name);
        $email = Email::fromString($eloquentUser->email);
        $passwordHash = PasswordHash::fromHash($eloquentUser->password);

        // Get organizational structure user ID (same as user identity ID)
        $organizationalStructureUserId = $userIdentityId->toString();

        // Use fromPersistence to reconstruct without triggering events
        return UserIdentity::fromPersistence(
            $userIdentityId,
            $organizationalStructureUserId,
            $username,
            $email,
            $passwordHash,
            (bool) $eloquentUser->is_change_password,
            (bool) $eloquentUser->is_only_login_ms,
            true, // Assume active if exists in database
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
