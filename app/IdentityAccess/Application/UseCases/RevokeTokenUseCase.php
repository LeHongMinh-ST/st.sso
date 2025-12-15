<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Domain\Events\TokenWasRevoked;
use App\IdentityAccess\Domain\Services\TokenGeneratorInterface;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for revoking an access token.
 *
 * SECURITY: Marks token as revoked, preventing further use.
 */
final class RevokeTokenUseCase
{
    /**
     * @param TokenGeneratorInterface $tokenGenerator
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute revoke token use case.
     *
     * SECURITY: Revokes token and stores domain event in outbox.
     *
     * @param string $token Token to revoke
     * @param string|null $userIdentityId User Identity ID (optional, for event)
     * @param string|null $clientId Client ID (optional, for event)
     * @return void
     */
    public function execute(string $token, ?string $userIdentityId = null, ?string $clientId = null): void
    {
        DB::transaction(function () use ($token, $userIdentityId, $clientId): void {
            // Validate token to get user info
            $tokenInfo = $this->tokenGenerator->validateToken($token);

            // Revoke token
            $this->tokenGenerator->revokeToken($token);

            // Store domain event in outbox if we have the info
            if (null !== $tokenInfo) {
                $event = new TokenWasRevoked(
                    $tokenInfo['user_identity_id'] ?? $userIdentityId ?? '',
                    $tokenInfo['client_id'] ?? $clientId ?? '',
                );

                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'user_identity',
                    $tokenInfo['user_identity_id'] ?? $userIdentityId ?? '',
                    $event::class,
                    $event->toPayload(),
                );
            }
        });
    }
}
