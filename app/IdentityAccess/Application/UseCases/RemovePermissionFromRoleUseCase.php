<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\RemovePermissionFromRoleDTO;
use App\IdentityAccess\Domain\Exceptions\PermissionNotFoundException;
use App\IdentityAccess\Domain\Exceptions\RoleNotFoundException;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\PermissionId;
use App\IdentityAccess\Domain\ValueObjects\RoleId;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for removing a permission from a role.
 */
final class RemovePermissionFromRoleUseCase
{
    /**
     * @param RoleRepositoryInterface $roleRepository
     * @param PermissionRepositoryInterface $permissionRepository
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionRepositoryInterface $permissionRepository,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute remove permission from role use case.
     *
     * @param RemovePermissionFromRoleDTO $dto Remove permission DTO
     * @return void
     * @throws RoleNotFoundException
     * @throws PermissionNotFoundException
     */
    public function execute(RemovePermissionFromRoleDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            // Find role
            $role = $this->roleRepository->findById(RoleId::fromString($dto->roleId));

            if (null === $role) {
                throw RoleNotFoundException::withId($dto->roleId);
            }

            // Find permission
            $permission = $this->permissionRepository->findById(PermissionId::fromString($dto->permissionId));

            if (null === $permission) {
                throw PermissionNotFoundException::withId($dto->permissionId);
            }

            // Remove permission from role
            $role->removePermission($dto->permissionId);

            // Save role (to persist domain events)
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
        });
    }
}
