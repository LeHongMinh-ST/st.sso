<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Outbox;

use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;

/**
 * Eloquent implementation of OutboxEventRepositoryInterface.
 * This implementation uses the OutboxEvent model for persistence.
 */
final class EloquentOutboxEventRepository implements OutboxEventRepositoryInterface
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
    ): void {
        OutboxEvent::create([
            'id' => $id,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'payload' => $payload,
        ]);
    }

    /**
     * Get unprocessed events ordered by creation time.
     *
     * @param int $limit Maximum number of events to retrieve
     * @param callable $callback Callback function to process each batch of events
     * @return void
     */
    public function getUnprocessedEvents(int $limit, callable $callback): void
    {
        OutboxEvent::whereNull('processed_at')
            ->orderBy('created_at')
            ->limit($limit)
            ->chunk(100, function ($events) use ($callback): void {
                $eventData = $events->map(fn (OutboxEvent $event): array => [
                    'id' => $event->id,
                    'aggregate_type' => $event->aggregate_type,
                    'aggregate_id' => $event->aggregate_id,
                    'event_type' => $event->event_type,
                    'payload' => $event->payload,
                    'created_at' => $event->created_at,
                ])->toArray();

                $callback($eventData);
            });
    }

    /**
     * Mark an event as processed.
     *
     * @param string $eventId Event UUID
     * @return void
     */
    public function markAsProcessed(string $eventId): void
    {
        $event = OutboxEvent::find($eventId);
        if (null !== $event) {
            $event->markAsProcessed();
        }
    }
}
