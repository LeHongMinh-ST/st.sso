<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\AuthenticateUserDTO;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use App\SharedKernel\Domain\ValueObjects\Email;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Use case for authenticating a user.
 *
 * SECURITY: Implements rate limiting, generic error messages, and timing-safe password verification.
 */
final class AuthenticateUserUseCase
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_MINUTES = 15;

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
     * Execute authentication use case.
     *
     * SECURITY:
     * - Rate limiting to prevent brute force attacks
     * - Generic error messages to prevent user enumeration
     * - Timing-safe password verification
     * - Domain events stored in outbox for reliable delivery
     *
     * @param AuthenticateUserDTO $dto Authentication DTO
     * @return UserIdentity Authenticated user identity
     * @throws InvalidCredentialsException
     */
    public function execute(AuthenticateUserDTO $dto): UserIdentity
    {
        // Rate limiting để prevent brute force attacks
        $key = 'authenticate:' . $dto->username;
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw InvalidCredentialsException::invalid();
        }

        return DB::transaction(function () use ($dto, $key) {
            // Find user identity by username or email
            $userIdentity = $this->findUserIdentity($dto->username);

            if (null === $userIdentity) {
                RateLimiter::hit($key, self::DECAY_MINUTES * 60);
                throw InvalidCredentialsException::invalid(); // Generic message
            }

            // Authenticate with timing-safe password verification
            $isAuthenticated = $userIdentity->authenticate(
                $dto->password,
                fn (string $plain, \App\IdentityAccess\Domain\ValueObjects\PasswordHash $hash) => $this->passwordHasher->verify($plain, $hash),
            );

            if (!$isAuthenticated) {
                RateLimiter::hit($key, self::DECAY_MINUTES * 60);
                throw InvalidCredentialsException::invalid(); // Generic message
            }

            // Clear rate limiter on success
            RateLimiter::clear($key);

            // Save user identity (to persist domain events)
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

    /**
     * Find user identity by username or email.
     *
     * SECURITY: Generic error handling to prevent user enumeration.
     *
     * @param string $username Username or email
     * @return UserIdentity|null
     */
    private function findUserIdentity(string $username): ?UserIdentity
    {
        // Try username first
        try {
            $usernameVO = Username::fromString($username);
            $userIdentity = $this->userIdentityRepository->findByUsername($usernameVO);
            if (null !== $userIdentity) {
                return $userIdentity;
            }
        } catch (Exception $e) {
            // Invalid username format, try email
        }

        // Try email
        try {
            $email = Email::fromString($username);
            $userIdentity = $this->userIdentityRepository->findByEmail($email);
            if (null !== $userIdentity) {
                return $userIdentity;
            }
        } catch (Exception $e) {
            // Invalid email format
        }

        return null;
    }
}
