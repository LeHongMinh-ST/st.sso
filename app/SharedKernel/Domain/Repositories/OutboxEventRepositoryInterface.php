<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Repositories;

/**
 * Repository interface for managing outbox events.
 * This interface belongs to Domain layer to avoid Application layer depending on Infrastructure.
 */
interface OutboxEventRepositoryInterface
{
    /**
     * Store an event in the outbox.
     *
     * @param string $id Event UUID
     * @param string $aggregateType Type of aggregate (e.g., 'user', 'faculty')
     * @param string $aggregateId Aggregate ID (UUID)
     * @param string $eventType Fully qualified class name of the event
     * @param array<string, mixed> $payload Event payload data
     * @return void
     */
    public function store(
        string $id,
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $payload
    ): void;

    /**
     * Get unprocessed events ordered by creation time.
     *
     * @param int $limit Maximum number of events to retrieve
     * @param callable $callback Callback function to process each batch of events
     * @return void
     */
    public function getUnprocessedEvents(int $limit, callable $callback): void;

    /**
     * Mark an event as processed.
     *
     * @param string $eventId Event UUID
     * @return void
     */
    public function markAsProcessed(string $eventId): void;
}
