<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\AssignPermissionToRoleDTO;
use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\Exceptions\RoleNotFoundException;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\RoleId;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for assigning permission to role.
 */
final class AssignPermissionToRoleUseCase
{
    /**
     * @param RoleRepositoryInterface $roleRepository
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute assign permission to role use case.
     *
     * @param AssignPermissionToRoleDTO $dto Assign permission DTO
     * @return Role Updated role aggregate
     * @throws RoleNotFoundException
     */
    public function execute(AssignPermissionToRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto) {
            // Find role
            $roleId = RoleId::fromString($dto->roleId);
            $role = $this->roleRepository->findById($roleId);

            if (null === $role) {
                throw RoleNotFoundException::withId($dto->roleId);
            }

            // Assign permission
            $role->assignPermission($dto->permissionId);

            // Save role
            $this->roleRepository->save($role);

            // Save domain events to outbox
            $events = $role->pullDomainEvents();
            foreach ($events as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'role',
                    $role->id()->toString(),
                    $event::class,
                    $event->toPayload(),
                );
            }

            return $role;
        });
    }
}
