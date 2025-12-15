<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\CreateDefaultCredentialsDTO;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating default credentials when a user is created.
 * This is typically triggered by UserWasCreated event from OrganizationalStructure context.
 *
 * SECURITY: Creates UserIdentity with hashed password, never stores plain password.
 */
final class CreateDefaultCredentialsUseCase
{
    /**
     * @param UserIdentityRepositoryInterface $userIdentityRepository
     * @param PasswordHasherInterface $passwordHasher
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute create default credentials use case.
     *
     * SECURITY: Password is hashed immediately, never stored as plain text.
     *
     * @param CreateDefaultCredentialsDTO $dto Create default credentials DTO
     * @return UserIdentity Created user identity
     */
    public function execute(CreateDefaultCredentialsDTO $dto): UserIdentity
    {
        return DB::transaction(function () use ($dto) {
            // Generate UserIdentity ID (same as OrganizationalStructure UserId)
            $userIdentityId = UserIdentityId::fromOrganizationalStructureUserId($dto->userId);

            // Create username from email (extract local part)
            $email = Email::fromString($dto->email);
            $username = Username::fromString($email->localPart());

            // Hash password immediately
            $passwordHash = $this->passwordHasher->hash($dto->defaultPassword);

            // Create UserIdentity aggregate
            $userIdentity = UserIdentity::create(
                $userIdentityId,
                $dto->userId, // Link to OrganizationalStructure UserId
                $username,
                $email,
                $passwordHash,
                true, // Must change password on first login
                false, // Not Microsoft-only login
            );

            // Save user identity
            $this->userIdentityRepository->save($userIdentity);

            // Save domain events to outbox
            $events = $userIdentity->pullDomainEvents();
            foreach ($events as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'user_identity',
                    $userIdentity->id()->toString(),
                    $event::class,
                    $event->toPayload(),
                );
            }

            return $userIdentity;
        });
    }
}
