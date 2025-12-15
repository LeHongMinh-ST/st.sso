<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Exceptions\UserIdentityNotFoundException;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for authenticating a user via Microsoft Azure.
 *
 * SECURITY: Handles Microsoft OAuth authentication securely.
 */
final class AuthenticateWithMicrosoftUseCase
{
    /**
     * @param UserIdentityRepositoryInterface $userIdentityRepository
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute Microsoft authentication use case.
     *
     * SECURITY:
     * - Validates user exists
     * - Checks if user can login via Microsoft
     * - Records authentication event
     *
     * @param string $email User email from Microsoft
     * @return UserIdentity Authenticated user identity
     * @throws UserIdentityNotFoundException
     */
    public function execute(string $email): UserIdentity
    {
        return DB::transaction(function () use ($email) {
            // Find user identity by email
            $emailVO = Email::fromString($email);
            $userIdentity = $this->userIdentityRepository->findByEmail($emailVO);

            if (null === $userIdentity) {
                throw UserIdentityNotFoundException::withEmail($email);
            }

            if (!$userIdentity->isActive()) {
                throw UserIdentityNotFoundException::withEmail($email);
            }

            // Record authentication event
            // Note: UserIdentity.authenticate() is for password auth, not Microsoft
            // So we manually record the event here
            $event = new \App\IdentityAccess\Domain\Events\UserWasAuthenticated(
                $userIdentity->id()->toString(),
                $userIdentity->organizationalStructureUserId(),
                $userIdentity->email()->toString(),
            );

            // Save domain event to outbox
            $this->outboxEventRepository->store(
                Str::uuid()->toString(),
                'user_identity',
                $userIdentity->id()->toString(),
                $event::class,
                $event->toPayload(),
            );

            return $userIdentity;
        });
    }
}
