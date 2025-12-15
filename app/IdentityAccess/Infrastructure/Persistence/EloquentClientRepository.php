<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Persistence;

use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Repositories\ClientRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\ClientSecret;
use App\Models\Client as EloquentClient;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of ClientRepositoryInterface.
 *
 * Note: Uses Laravel Passport Client model which uses string IDs.
 */
final class EloquentClientRepository implements ClientRepositoryInterface
{
    /**
     * Generate a new Client ID.
     *
     * Note: Laravel Passport uses string IDs for clients.
     *
     * @return ClientId
     */
    public function nextIdentity(): ClientId
    {
        // Laravel Passport generates numeric string IDs
        // We'll use a UUID-based approach for consistency
        return ClientId::fromString((string) DB::table('oauth_clients')->max('id') + 1);
    }

    /**
     * Find Client by ID.
     *
     * @param ClientId $id Client ID
     * @return Client|null
     */
    public function findById(ClientId $id): ?Client
    {
        $eloquentClient = EloquentClient::find($id->toString());

        if (null === $eloquentClient) {
            return null;
        }

        return $this->toDomain($eloquentClient);
    }

    /**
     * Find all clients.
     *
     * @return array<Client>
     */
    public function findAll(): array
    {
        $eloquentClients = EloquentClient::all();

        return $eloquentClients->map(fn ($client) => $this->toDomain($client))->toArray();
    }

    /**
     * Save Client aggregate.
     *
     * @param Client $client Client aggregate
     * @return void
     */
    public function save(Client $client): void
    {
        DB::transaction(function () use ($client): void {
            $eloquentClient = EloquentClient::find($client->id()->toString());

            if (null === $eloquentClient) {
                $eloquentClient = new EloquentClient();
                $eloquentClient->id = $client->id()->toString();
            }

            $eloquentClient->name = $client->name();
            $eloquentClient->description = $client->description();
            $eloquentClient->thumbnail = $client->thumbnail();
            $eloquentClient->redirect = $client->redirectUri();
            $eloquentClient->secret = $client->secret()->toString();
            $eloquentClient->revoked = $client->isRevoked();
            $eloquentClient->personal_access_client = $client->isPersonalAccessClient();
            $eloquentClient->password_client = $client->isPasswordClient();
            $eloquentClient->allowed_roles = $client->allowedRoles();
            $eloquentClient->grant_types = $client->grantTypes();
            $eloquentClient->scopes = $client->scopes();
            $eloquentClient->provider = $client->provider();

            $eloquentClient->save();
        });
    }

    /**
     * Delete Client aggregate.
     *
     * @param ClientId $id Client ID
     * @return void
     */
    public function delete(ClientId $id): void
    {
        EloquentClient::destroy($id->toString());
    }

    /**
     * Map Eloquent model to Domain aggregate.
     *
     * @param EloquentClient $eloquentClient Eloquent client model
     * @return Client Client aggregate
     */
    private function toDomain(EloquentClient $eloquentClient): Client
    {
        $clientId = ClientId::fromString((string) $eloquentClient->id);
        $clientSecret = ClientSecret::fromString($eloquentClient->secret ?? '');

        // Use fromPersistence to reconstruct without triggering events
        return Client::fromPersistence(
            $clientId,
            $eloquentClient->name,
            $eloquentClient->redirect ?? '',
            $clientSecret,
            (bool) $eloquentClient->revoked,
            (bool) $eloquentClient->personal_access_client,
            (bool) $eloquentClient->password_client,
            $eloquentClient->allowed_roles ?? [],
            $eloquentClient->grant_types ?? [],
            $eloquentClient->scopes ?? [],
            $eloquentClient->description,
            $eloquentClient->thumbnail,
            $eloquentClient->provider,
        );
    }
}
