<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * Use case for finding users with filters and pagination.
 */
final class FindUsersUseCase
{
    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Find users with filters and pagination.
     *
     * @param array<string, mixed> $filters Filters (faculty_id, department_id, search, role)
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return LengthAwarePaginator Paginated users
     */
    public function execute(
        array $filters = [],
        int $page = 1,
        int $perPage = 15
    ): LengthAwarePaginator {
        // Build query
        $query = EloquentUser::query();

        // Apply filters
        if (isset($filters['faculty_id']) && null !== $filters['faculty_id']) {
            $query->where('faculty_id', $filters['faculty_id']);
        }

        if (isset($filters['department_id']) && null !== $filters['department_id']) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['search']) && null !== $filters['search'] && '' !== $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['roles']) && is_array($filters['roles']) && count($filters['roles']) > 0) {
            $query->whereHas('roles', function ($q) use ($filters): void {
                $q->whereIn('name', $filters['roles']);
            });
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Eager load relationships to avoid N+1 queries
        $query->with(['faculty', 'department']);

        // Paginate
        $eloquentUsers = $query->paginate($perPage, ['*'], 'page', $page);

        // Convert Eloquent models to Domain aggregates
        // Note: This still causes N+1 queries because we call findById for each user
        // TODO: Optimize by batch loading or caching
        $users = [];
        foreach ($eloquentUsers->items() as $eloquentUser) {
            $user = $this->convertToDomainAggregate($eloquentUser);
            if (null !== $user) {
                $users[] = $user;
            }
        }

        // Create new paginator with Domain aggregates
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $users,
            $eloquentUsers->total(),
            $eloquentUsers->perPage(),
            $eloquentUsers->currentPage(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Convert Eloquent User to Domain User aggregate.
     * Temporary bridge method until full migration.
     *
     * @param EloquentUser $eloquentUser Eloquent User model
     * @return User|null Domain User aggregate
     */
    private function convertToDomainAggregate(EloquentUser $eloquentUser): ?User
    {
        try {
            // Try to find by UUID first
            $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'uuid');
            $userId = null;

            if ($hasUuidColumn && null !== $eloquentUser->uuid) {
                $userId = \App\OrganizationalStructure\Domain\ValueObjects\UserId::fromString($eloquentUser->uuid);
            } else {
                // Fallback: generate deterministic UUID from integer ID
                $deterministicUuid = $this->generateDeterministicUuid('users', $eloquentUser->id);
                $userId = \App\OrganizationalStructure\Domain\ValueObjects\UserId::fromString($deterministicUuid);
            }

            // Find user aggregate from repository
            return $this->userRepository->findById($userId);
        } catch (Exception $e) {
            // Log error but don't fail
            \Illuminate\Support\Facades\Log::warning('Failed to convert Eloquent User to Domain aggregate', [
                'user_id' => $eloquentUser->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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
