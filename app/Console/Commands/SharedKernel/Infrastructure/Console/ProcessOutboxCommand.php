<?php

declare(strict_types=1);

namespace App\Console\Commands\SharedKernel\Infrastructure\Console;

use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Log;
use ReflectionClass;
use RuntimeException;
use Throwable;

/**
 * Command to process outbox events.
 * This command should be run periodically (e.g., every minute) via Laravel Scheduler.
 */
final class ProcessOutboxCommand extends Command
{
    protected $signature = 'outbox:process {--limit=100 : Number of events to process}';

    protected $description = 'Process unprocessed events from outbox';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $processedCount = 0;

        OutboxEvent::whereNull('processed_at')
            ->orderBy('created_at')
            ->limit($limit)
            ->chunk(100, function ($events) use (&$processedCount): void {
                foreach ($events as $outboxEvent) {
                    try {
                        $this->processEvent($outboxEvent);
                        $outboxEvent->markAsProcessed();
                        $processedCount++;
                    } catch (Throwable $e) {
                        $this->error("Failed to process event {$outboxEvent->id}: {$e->getMessage()}");
                        // Log error but continue processing other events
                        Log::error('Outbox event processing failed', [
                            'event_id' => $outboxEvent->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
            });

        $this->info("Processed {$processedCount} events");

        return Command::SUCCESS;
    }

    /**
     * Process a single outbox event.
     *
     * @param OutboxEvent $outboxEvent
     * @return void
     */
    private function processEvent(OutboxEvent $outboxEvent): void
    {
        // Reconstitute domain event from payload
        $eventClass = $outboxEvent->event_type;

        if (!class_exists($eventClass)) {
            throw new RuntimeException("Event class {$eventClass} does not exist");
        }

        // Create event instance from payload
        // This assumes event has a static factory method or constructor that accepts array
        $event = $this->reconstituteEvent($eventClass, $outboxEvent->payload);

        // Dispatch event using Laravel's event system
        Event::dispatch($event);
    }

    /**
     * Reconstitute domain event from payload.
     *
     * @param string $eventClass
     * @param array<string, mixed> $payload
     * @return object
     */
    private function reconstituteEvent(string $eventClass, array $payload): object
    {
        // Try to use reflection to create event instance
        $reflection = new ReflectionClass($eventClass);

        // Check if event has a static factory method
        if ($reflection->hasMethod('fromPayload')) {
            return $eventClass::fromPayload($payload);
        }

        // Otherwise, try to create using constructor
        if (null !== $reflection->getConstructor()) {
            return $reflection->newInstanceArgs($this->mapPayloadToConstructorArgs($reflection, $payload));
        }

        throw new RuntimeException("Cannot reconstitute event {$eventClass} from payload");
    }

    /**
     * Map payload array to constructor arguments.
     *
     * @param ReflectionClass $reflection
     * @param array<string, mixed> $payload
     * @return array<mixed>
     */
    private function mapPayloadToConstructorArgs(ReflectionClass $reflection, array $payload): array
    {
        $constructor = $reflection->getConstructor();
        if (null === $constructor) {
            return [];
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            if (isset($payload[$paramName])) {
                $args[] = $payload[$paramName];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException("Missing required parameter {$paramName} for event {$reflection->getName()}");
            }
        }

        return $args;
    }
}
