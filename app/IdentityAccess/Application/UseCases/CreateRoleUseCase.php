<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\CreateRoleDTO;
use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating a role.
 */
final class CreateRoleUseCase
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
     * Execute create role use case.
     *
     * @param CreateRoleDTO $dto Create role DTO
     * @return Role Created role aggregate
     */
    public function execute(CreateRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto) {
            // Generate role ID
            $roleId = $this->roleRepository->nextIdentity();

            // Create role aggregate
            $role = Role::create(
                $roleId,
                $dto->name,
                $dto->displayName,
                $dto->description,
            );

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
