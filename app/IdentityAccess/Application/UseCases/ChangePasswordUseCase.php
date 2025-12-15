<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\ChangePasswordDTO;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
use App\IdentityAccess\Domain\Exceptions\UserIdentityNotFoundException;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for changing user password.
 *
 * SECURITY: Verifies current password before allowing change.
 */
final class ChangePasswordUseCase
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
     * Execute change password use case.
     *
     * SECURITY:
     * - Verifies current password before allowing change
     * - New password is hashed immediately
     * - Domain events stored in outbox
     *
     * @param string $userIdentityId User Identity ID (UUID)
     * @param ChangePasswordDTO $dto Change password DTO
     * @return UserIdentity Updated user identity
     * @throws UserIdentityNotFoundException
     * @throws InvalidCredentialsException
     */
    public function execute(string $userIdentityId, ChangePasswordDTO $dto): UserIdentity
    {
        return DB::transaction(function () use ($userIdentityId, $dto) {
            // Find user identity
            $userIdentityIdVO = UserIdentityId::fromString($userIdentityId);
            $userIdentity = $this->userIdentityRepository->findById($userIdentityIdVO);

            if (null === $userIdentity) {
                throw UserIdentityNotFoundException::withId($userIdentityId);
            }

            // Verify current password
            $isCurrentPasswordValid = $this->passwordHasher->verify(
                $dto->currentPassword,
                $userIdentity->passwordHash(),
            );

            if (!$isCurrentPasswordValid) {
                throw InvalidCredentialsException::invalid();
            }

            // Hash new password
            $newPasswordHash = $this->passwordHasher->hash($dto->newPassword);

            // Change password
            $userIdentity->changePassword($newPasswordHash);

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
