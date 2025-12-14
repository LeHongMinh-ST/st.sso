<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for assigning a user to a faculty.
 */
final class AssignUserToFacultyUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Execute assign user to faculty use case.
     *
     * @param string $userId User ID (UUID)
     * @param string $facultyId Faculty ID (UUID)
     * @return User Updated user aggregate
     * @throws UserNotFoundException
     */
    public function execute(string $userId, string $facultyId): User
    {
        return DB::transaction(function () use ($userId, $facultyId) {
            $user = $this->userRepository->findById(UserId::fromString($userId));

            if (null === $user) {
                throw UserNotFoundException::withId($userId);
            }

            // Assign user to faculty
            $user->assignToFaculty(FacultyId::fromString($facultyId));

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
