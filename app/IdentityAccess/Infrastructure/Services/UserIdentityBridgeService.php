<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Services;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge service to link UserIdentity với User model.
 * TEMPORARY: This will be removed after full migration.
 *
 * Strategy:
 * - During migration: Bridge between DDD aggregates và Eloquent models
 * - After migration: Remove bridge, use DDD aggregates directly
 * - Removal target: Phase 6
 */
final class UserIdentityBridgeService
{
    /**
     * @param UserIdentityRepositoryInterface $userIdentityRepository
     */
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
    ) {
    }

    /**
     * Get Eloquent User model from UserIdentity.
     * Used for Laravel Auth system compatibility during migration.
     *
     * @param UserIdentity $userIdentity User Identity aggregate
     * @return EloquentUser|null Eloquent User model or null if not found
     */
    public function getEloquentUser(UserIdentity $userIdentity): ?EloquentUser
    {
        $organizationalStructureUserId = $userIdentity->organizationalStructureUserId();

        // Try to find by UUID first
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn) {
            $user = EloquentUser::where('uuid', $organizationalStructureUserId)->first();
            if (null !== $user) {
                return $user;
            }
        }

        // Fallback: try to find by email
        return EloquentUser::where('email', (string) $userIdentity->email())->first();
    }

    /**
     * Get UserIdentity from Eloquent User model.
     * Used to convert existing Eloquent User to UserIdentity.
     *
     * @param EloquentUser $user Eloquent User model
     * @return UserIdentity|null User Identity aggregate or null if not found
     */
    public function getUserIdentity(EloquentUser $user): ?UserIdentity
    {
        $userIdentityId = $this->getUserIdentityId($user);

        return $this->userIdentityRepository->findById($userIdentityId);
    }

    /**
     * Get UserIdentityId from Eloquent User.
     *
     * @param EloquentUser $user Eloquent User model
     * @return UserIdentityId User Identity ID
     */
    public function getUserIdentityId(EloquentUser $user): UserIdentityId
    {
        // Try to get UUID from user
        $hasUuidColumn = Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn && null !== $user->uuid) {
            return UserIdentityId::fromString($user->uuid);
        }

        // Fallback: generate deterministic UUID from integer ID
        $deterministicUuid = $this->generateDeterministicUuid('users', $user->id);

        return UserIdentityId::fromString($deterministicUuid);
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
