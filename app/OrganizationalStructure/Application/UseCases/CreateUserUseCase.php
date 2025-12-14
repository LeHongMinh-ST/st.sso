<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\SharedKernel\Domain\ValueObjects\Email;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating a new user.
 */
final class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Execute create user use case.
     *
     * @param CreateUserDTO $dto Create user DTO
     * @return User Created user aggregate
     * @throws UserAlreadyExistsException
     */
    public function execute(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $email = Email::fromString($dto->email);
            $userName = UserName::fromString($dto->userName);

            // Check if user already exists
            $existingUser = $this->userRepository->findByEmail($email);
            if (null !== $existingUser) {
                throw UserAlreadyExistsException::withEmail($dto->email);
            }

            $existingUser = $this->userRepository->findByUserName($userName);
            if (null !== $existingUser) {
                throw UserAlreadyExistsException::withUserName($dto->userName);
            }

            if (null !== $dto->userCode) {
                $userCode = UserCode::fromString($dto->userCode);
                $existingUser = $this->userRepository->findByUserCode($userCode);
                if (null !== $existingUser) {
                    throw UserAlreadyExistsException::withUserCode($dto->userCode);
                }
            }

            // Create user aggregate
            $userId = UserId::generate();
            $fullName = FullName::fromParts($dto->firstName, $dto->lastName);
            $userCode = $dto->userCode ? UserCode::fromString($dto->userCode) : null;
            $phoneNumber = $dto->phone ? PhoneNumber::fromString($dto->phone) : null;
            $facultyId = $dto->facultyId ? FacultyId::fromString($dto->facultyId) : null;
            $departmentId = $dto->departmentId ? DepartmentId::fromString($dto->departmentId) : null;

            $user = User::create(
                $userId,
                $userName,
                $fullName,
                $email,
                $userCode,
                $phoneNumber,
                $facultyId,
                $departmentId,
            );

            // Save user aggregate
            $this->userRepository->save($user);

            // Save domain events to outbox
            $events = $user->pullDomainEvents();
            foreach ($events as $event) {
                OutboxEvent::create([
                    'id' => Str::uuid()->toString(),
                    'aggregate_type' => 'user',
                    'aggregate_id' => $userId->toString(),
                    'event_type' => $event::class,
                    'payload' => $event->toPayload(),
                ]);
            }

            return $user;
        });
    }
}
