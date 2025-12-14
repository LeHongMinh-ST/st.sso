<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\SharedKernel\Domain\ValueObjects\Email;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for updating user profile.
 */
final class UpdateUserProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Execute update user profile use case.
     *
     * @param string $userId User ID (UUID)
     * @param UpdateUserProfileDTO $dto Update user profile DTO
     * @return User Updated user aggregate
     * @throws UserNotFoundException
     */
    public function execute(string $userId, UpdateUserProfileDTO $dto): User
    {
        return DB::transaction(function () use ($userId, $dto) {
            $user = $this->userRepository->findById(UserId::fromString($userId));

            if (null === $user) {
                throw UserNotFoundException::withId($userId);
            }

            // Update user profile
            $fullName = FullName::fromParts($dto->firstName, $dto->lastName);
            $email = Email::fromString($dto->email);
            $phoneNumber = $dto->phone ? PhoneNumber::fromString($dto->phone) : null;

            $user->updateProfile($fullName, $email, $phoneNumber);

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
