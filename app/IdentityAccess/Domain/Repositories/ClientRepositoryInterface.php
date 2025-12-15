<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Repositories;

use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\ValueObjects\ClientId;

/**
 * Client Repository Interface.
 * Defines contract for persisting and retrieving Client aggregates.
 */
interface ClientRepositoryInterface
{
    /**
     * Generate a new Client ID.
     *
     * @return ClientId
     */
    public function nextIdentity(): ClientId;

    /**
     * Find Client by ID.
     *
     * @param ClientId $id Client ID
     * @return Client|null
     */
    public function findById(ClientId $id): ?Client;

    /**
     * Find all clients.
     *
     * @return array<Client>
     */
    public function findAll(): array;

    /**
     * Save Client aggregate.
     *
     * @param Client $client Client aggregate
     * @return void
     */
    public function save(Client $client): void;

    /**
     * Delete Client aggregate.
     *
     * @param ClientId $id Client ID
     * @return void
     */
    public function delete(ClientId $id): void;
}
