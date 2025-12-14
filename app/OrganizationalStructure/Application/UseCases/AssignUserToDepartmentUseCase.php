<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for assigning a user to a department.
 */
final class AssignUserToDepartmentUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Execute assign user to department use case.
     *
     * @param string $userId User ID (UUID)
     * @param string $departmentId Department ID (UUID)
     * @return User Updated user aggregate
     * @throws UserNotFoundException
     */
    public function execute(string $userId, string $departmentId): User
    {
        return DB::transaction(function () use ($userId, $departmentId) {
            $user = $this->userRepository->findById(UserId::fromString($userId));

            if (null === $user) {
                throw UserNotFoundException::withId($userId);
            }

            // Assign user to department
            $user->assignToDepartment(DepartmentId::fromString($departmentId));

            // Save user aggregate
            $this->userRepository->save($user);

            // Save domain events to outbox
            $events = $user->pullDomainEvents();
            foreach ($events as $event) {
                OutboxEvent::create([
                    'id' => Str::uuid()->toString(),
                    'aggregate_type' => 'user',
                    'aggregate_id' => $userId,
                    'event_type' => $event::class,
                    'payload' => $event->toPayload(),
                ]);
            }

            return $user;
        });
    }
}
