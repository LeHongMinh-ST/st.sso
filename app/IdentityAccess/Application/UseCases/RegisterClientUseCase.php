<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\UseCases;

use App\IdentityAccess\Application\DTOs\RegisterClientDTO;
use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Repositories\ClientRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\ClientSecret;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for registering an OAuth2 client.
 *
 * SECURITY: Generates secure client secret.
 */
final class RegisterClientUseCase
{
    /**
     * @param ClientRepositoryInterface $clientRepository
     * @param OutboxEventRepositoryInterface $outboxEventRepository
     */
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute register client use case.
     *
     * SECURITY: Generates cryptographically secure client secret.
     *
     * @param RegisterClientDTO $dto Register client DTO
     * @return Client Registered client aggregate
     */
    public function execute(RegisterClientDTO $dto): Client
    {
        return DB::transaction(function () use ($dto) {
            // Generate client ID and secret
            $clientId = $this->clientRepository->nextIdentity();
            $clientSecret = ClientSecret::generate();

            // Create client aggregate
            $client = Client::register(
                $clientId,
                $dto->name,
                $dto->redirectUri,
                $clientSecret,
                $dto->isPersonalAccessClient,
                $dto->isPasswordClient,
                $dto->allowedRoles,
                $dto->grantTypes,
                $dto->scopes,
                $dto->description,
                $dto->thumbnail,
                $dto->provider,
            );

            // Save client
            $this->clientRepository->save($client);

            // Save domain events to outbox
            $events = $client->pullDomainEvents();
            foreach ($events as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'client',
                    $client->id()->toString(),
                    $event::class,
                    $event->toPayload(),
                );
            }

            return $client;
        });
    }
}
