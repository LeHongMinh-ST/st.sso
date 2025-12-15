# ADR 004: Outbox Pattern Implementation

**Status**: Accepted  
**Date**: 2024-12-14  
**Deciders**: Architecture Team  
**Context**: Implementation của Outbox Pattern để đảm bảo reliable event delivery trong Event-Driven Architecture

## Context

Khi publish Domain Events, chúng ta cần đảm bảo:
- Events được publish **sau khi** business transaction commit thành công
- Events không bị mất nếu application crash trước khi publish
- Events được publish **exactly once** (không duplicate)

Traditional approach (publish events directly) có vấn đề:
- Nếu application crash sau khi commit transaction nhưng trước khi publish event → event bị mất
- Nếu publish event trong transaction → có thể rollback event nếu transaction fails
- Nếu publish event sau transaction → có thể mất event nếu application crashes

## Decision

Chúng ta sẽ implement **Transactional Outbox Pattern**:

1. **Store events in outbox**: Khi domain operation tạo events, events được lưu vào `outbox_events` table trong **cùng transaction** với business data
2. **Background processing**: Background job (`ProcessOutboxCommand`) process events từ outbox và dispatch chúng
3. **Mark as processed**: Event được đánh dấu `processed_at` sau khi dispatch thành công

## Implementation

### Database Schema

```sql
CREATE TABLE outbox_events (
    id VARCHAR(36) PRIMARY KEY,
    aggregate_type VARCHAR(255) NOT NULL,
    aggregate_id VARCHAR(36) NOT NULL,
    event_type VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    created_at TIMESTAMP NOT NULL,
    processed_at TIMESTAMP NULL,
    INDEX idx_processed_at (processed_at),
    INDEX idx_created_at (created_at)
);
```

**Fields**:
- `id`: Unique event ID (UUID)
- `aggregate_type`: Type of aggregate (e.g., 'user_identity', 'role')
- `aggregate_id`: ID of aggregate that generated the event (UUID)
- `event_type`: Fully qualified class name of the event
- `payload`: JSON serialized event data
- `created_at`: When event was created
- `processed_at`: When event was processed (NULL if not processed yet)

**Indexes**:
- `idx_processed_at`: For querying unprocessed events (`WHERE processed_at IS NULL`)
- `idx_created_at`: For ordering events by creation time

### Repository Interface

```php
interface OutboxEventRepositoryInterface
{
    public function store(
        string $id,
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $payload,
    ): void;

    public function getUnprocessed(int $limit = 100): array;

    public function markAsProcessed(string $id): void;
}
```

### Eloquent Model

```php
final class OutboxEvent extends Model
{
    protected $table = 'outbox_events';
    
    protected $fillable = [
        'id',
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'payload',
        'created_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
```

### Use Case Integration

Use Cases store events vào outbox trong cùng transaction:

```php
final class ChangePasswordUseCase
{
    public function execute(ChangePasswordDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            // 1. Load aggregate
            $userIdentity = $this->userIdentityRepository->findById(...);
            
            // 2. Perform domain operation (creates domain events)
            $userIdentity->changePassword(...);
            
            // 3. Save aggregate (persists business data)
            $this->userIdentityRepository->save($userIdentity);

            // 4. Store events in outbox (same transaction)
            foreach ($userIdentity->pullDomainEvents() as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'user_identity',
                    $userIdentity->id()->toString(),
                    $event::class,
                    $event->toPayload(),
                );
            }
            // Transaction commits → both business data and events are persisted
        });
    }
}
```

### Background Processing

Background command processes outbox events:

```php
final class ProcessOutboxCommand extends Command
{
    protected $signature = 'outbox:process';
    protected $description = 'Process outbox events';

    public function handle(): void
    {
        $events = $this->outboxEventRepository->getUnprocessed(100);

        foreach ($events as $event) {
            try {
                // 1. Deserialize event
                $domainEvent = $this->eventFactory->create($event);
                
                // 2. Dispatch event
                $this->eventDispatcher->dispatch($domainEvent);
                
                // 3. Mark as processed
                $this->outboxEventRepository->markAsProcessed($event->id);
            } catch (\Throwable $e) {
                Log::error('Failed to process outbox event', [
                    'event_id' => $event->id,
                    'event_type' => $event->event_type,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't mark as processed → will retry on next run
            }
        }
    }
}
```

**Scheduling**: Command chạy mỗi phút (có thể config):

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('outbox:process')
        ->everyMinute()
        ->withoutOverlapping();
}
```

## Rationale

### Tại sao chọn Outbox Pattern?

1. **Reliability**: Events được lưu trong cùng transaction với business data → atomicity
2. **Simplicity**: Không cần external message queue (có thể migrate sau nếu cần)
3. **Exactly-once delivery**: Có thể implement idempotency checks trong event listeners
4. **Laravel Integration**: Dễ integrate với Laravel's queue system và commands

### Tại sao không publish events trực tiếp?

**Problem**: Nếu publish events trực tiếp:
- **Before transaction commit**: Events có thể được publish nhưng transaction rollback → inconsistent state
- **After transaction commit**: Nếu application crash sau commit nhưng trước publish → events bị mất

**Solution**: Outbox Pattern đảm bảo events được lưu trong cùng transaction → atomicity

### Tại sao background processing?

**Problem**: Nếu process events synchronously:
- Blocks main operation
- Slow response time
- Difficult to retry failed events

**Solution**: Background processing:
- Non-blocking
- Can retry failed events
- Can scale independently

## Consequences

### Positive

- ✅ **Reliability**: Events không bị mất (stored in same transaction)
- ✅ **Atomicity**: Business data và events được commit cùng lúc
- ✅ **Exactly-once delivery**: Có thể implement idempotency checks
- ✅ **Simplicity**: Không cần external message queue
- ✅ **Retry mechanism**: Failed events được retry automatically

### Negative

- ⚠️ **Latency**: Events không được publish immediately (background processing)
- ⚠️ **Eventual Consistency**: Side effects có thể không immediate
- ⚠️ **Background Job**: Cần background job để process outbox
- ⚠️ **Database Load**: Outbox table có thể grow large (need cleanup strategy)

### Mitigation

- ✅ **Fast Processing**: Process outbox frequently (every minute)
- ✅ **Idempotency**: Implement idempotency checks trong event listeners
- ✅ **Monitoring**: Monitor outbox processing và event dispatch
- ✅ **Cleanup**: Clean up processed events older than X days
- ✅ **Alerting**: Alert if unprocessed events accumulate

## Alternatives Considered

### 1. Direct Event Publishing

**Rejected**: 
- Events có thể bị mất nếu application crashes
- No guarantee of delivery

### 2. Message Queue (RabbitMQ, Kafka)

**Rejected**:
- Adds external dependency
- Over-engineering cho hệ thống hiện tại
- Outbox Pattern đơn giản hơn và đủ cho requirements
- Có thể migrate sang message queue sau nếu cần scale

### 3. Database Triggers

**Rejected**:
- Database-specific (not portable)
- Difficult to test và debug
- Limited flexibility

### 4. Two-Phase Commit

**Rejected**:
- Complex và difficult to implement
- Over-engineering cho use case hiện tại
- Outbox Pattern đơn giản hơn và đủ

## Implementation Notes

### Event Factory

Event factory deserializes events từ outbox:

```php
final class DomainEventFactory
{
    public function create(OutboxEvent $outboxEvent): DomainEvent
    {
        $eventClass = $outboxEvent->event_type;
        
        if (!class_exists($eventClass)) {
            throw new \RuntimeException("Event class not found: {$eventClass}");
        }

        if (!is_subclass_of($eventClass, DomainEvent::class)) {
            throw new \RuntimeException("Invalid event class: {$eventClass}");
        }

        // Deserialize event from payload
        return $eventClass::fromPayload($outboxEvent->payload);
    }
}
```

### Idempotency

Event listeners nên implement idempotency checks:

```php
final class SendWelcomeEmailListener
{
    public function handle(UserIdentityWasCreated $event): void
    {
        // Check if email already sent (idempotency check)
        if ($this->emailService->wasEmailSent($event->userIdentityId(), 'welcome')) {
            return; // Already sent, skip
        }

        // Send email
        $this->emailService->sendWelcomeEmail($event->userIdentityId());
        
        // Mark as sent
        $this->emailService->markEmailSent($event->userIdentityId(), 'welcome');
    }
}
```

### Cleanup Strategy

Clean up processed events older than 30 days:

```php
final class CleanupOutboxCommand extends Command
{
    public function handle(): void
    {
        $this->outboxEventRepository->deleteProcessedOlderThan(
            now()->subDays(30)
        );
    }
}
```

Schedule cleanup weekly:

```php
$schedule->command('outbox:cleanup')
    ->weekly();
```

## References

- [Transactional Outbox Pattern](https://microservices.io/patterns/data/transactional-outbox.html)
- [Reliable Event Processing](https://martinfowler.com/articles/201701-event-driven.html)
- [Outbox Pattern in Laravel](https://laravel.com/docs/queues)
