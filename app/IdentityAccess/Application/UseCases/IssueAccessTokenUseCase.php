<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\IssueTokenDTO;
use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Events\TokenWasIssued;
use App\IdentityAccess\Domain\Exceptions\ClientNotFoundException;
use App\IdentityAccess\Domain\Exceptions\UserIdentityNotFoundException;
use App\IdentityAccess\Domain\Repositories\ClientRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\TokenGeneratorInterface;
use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for issuing an access token.
 *
 * SECURITY: Validates user and client before issuing token.
 */
final class IssueAccessTokenUseCase
{
    /**
     * @param UserIdentityRepositoryInterface $userIdentityRepository
     * @param ClientRepositoryInterface $clientRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly UserIdentityRepositoryInterface $userIdentityRepository,
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute issue access token use case.
     *
     * SECURITY:
     * - Validates user identity exists and is active
     * - Validates client exists and is not revoked
     * - Generates secure token with proper expiration
     * - Domain events stored in outbox
     *
     * @param IssueTokenDTO $dto Issue token DTO
     * @return string Access token
     * @throws UserIdentityNotFoundException
     * @throws ClientNotFoundException
     */
    public function execute(IssueTokenDTO $dto): string
    {
        return DB::transaction(function () use ($dto) {
            // Find user identity
            $userIdentityId = UserIdentityId::fromString($dto->userIdentityId);
            $userIdentity = $this->userIdentityRepository->findById($userIdentityId);

            if (null === $userIdentity) {
                throw UserIdentityNotFoundException::withId($dto->userIdentityId);
            }

            if (!$userIdentity->isActive()) {
                throw UserIdentityNotFoundException::withId($dto->userIdentityId);
            }

            // Find client
            $clientId = ClientId::fromString($dto->clientId);
            $client = $this->clientRepository->findById($clientId);

            if (null === $client) {
                throw ClientNotFoundException::withId($dto->clientId);
            }

            if ($client->isRevoked()) {
                throw ClientNotFoundException::withId($dto->clientId);
            }

            // Generate access token
            $accessToken = $this->tokenGenerator->generateAccessToken(
                $userIdentity,
                $client,
                $dto->scopes,
            );

            // Store domain event in outbox
            $event = new TokenWasIssued(
                $userIdentity->id()->toString(),
                $client->id()->toString(),
                $dto->scopes,
            );

            $this->outboxEventRepository->store(
                Str::uuid()->toString(),
                'user_identity',
                $userIdentity->id()->toString(),
                $event::class,
                $event->toPayload(),
            );

            return $accessToken;
        });
    }
}
