# ADR 003: Event-Driven Architecture

**Status**: Accepted  
**Date**: 2024-12-14  
**Deciders**: Architecture Team  
**Context**: Migration từ monolithic Laravel application sang Domain-Driven Design (DDD) architecture

## Context

Trong DDD architecture, chúng ta cần cách để communicate giữa các Bounded Contexts và handle side effects sau khi domain operations hoàn tất. Traditional synchronous calls tạo tight coupling và khó scale.

## Decision

Chúng ta sẽ sử dụng **Event-Driven Architecture** với **Domain Events** và **Outbox Pattern** để:

1. **Decouple contexts**: Contexts communicate qua events, không direct calls
2. **Handle side effects**: Side effects (emails, notifications, cache invalidation) được handle asynchronously
3. **Ensure reliability**: Outbox Pattern đảm bảo events không bị mất và được deliver exactly once

## Domain Events

### IdentityAccess Context Events

**UserIdentity Events**:
- `UserIdentityWasCreated`: Khi user identity được tạo
- `PasswordWasChanged`: Khi password được thay đổi
- `UserWasAuthenticated`: Khi user đăng nhập thành công

**Role & Permission Events**:
- `RoleWasCreated`: Khi role được tạo
- `PermissionWasCreated`: Khi permission được tạo
- `PermissionWasAssignedToRole`: Khi permission được assign cho role
- `PermissionWasRemovedFromRole`: Khi permission được remove khỏi role

**Client Events**:
- `ClientWasRegistered`: Khi OAuth client được register

**Token Events**:
- `TokenWasIssued`: Khi access token được issue
- `TokenWasRevoked`: Khi token được revoke

### OrganizationalStructure Context Events

**User Events**:
- `UserWasCreated`: Khi user profile được tạo
- `UserProfileWasUpdated`: Khi user profile được update
- `UserWasAssignedToFaculty`: Khi user được assign vào faculty
- `UserWasAssignedToDepartment`: Khi user được assign vào department

**Faculty Events**:
- `FacultyWasCreated`: Khi faculty được tạo
- `FacultyWasUpdated`: Khi faculty được update

**Department Events**:
- `DepartmentWasCreated`: Khi department được tạo
- `DepartmentWasUpdated`: Khi department được update

## Event Structure

Tất cả Domain Events implement interface:

```php
interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
    public function toPayload(): array;
}
```

**Example**:
```php
final class UserIdentityWasCreated implements DomainEvent
{
    public function __construct(
        private readonly string $userIdentityId,
        private readonly string $email,
        private readonly \DateTimeImmutable $occurredOn,
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function toPayload(): array
    {
        return [
            'user_identity_id' => $this->userIdentityId,
            'email' => $this->email,
        ];
    }
}
```

## Outbox Pattern

### Problem

Khi publish Domain Events, chúng ta cần đảm bảo:
- Events được publish **sau khi** business transaction commit thành công
- Events không bị mất nếu application crash trước khi publish
- Events được publish **exactly once** (không duplicate)

### Solution: Transactional Outbox Pattern

1. **Store events in outbox**: Khi domain operation tạo events, events được lưu vào `outbox_events` table trong **cùng transaction** với business data
2. **Background processing**: Background job (`ProcessOutboxCommand`) process events từ outbox và dispatch chúng
3. **Mark as processed**: Event được đánh dấu `processed_at` sau khi dispatch thành công

### Outbox Schema

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

### Processing Flow

1. **Domain Operation**: Aggregate tạo domain event và record nó
2. **Use Case**: Use case stores event vào outbox trong cùng transaction
3. **Transaction Commit**: Business data và events được commit cùng lúc
4. **Background Job**: `ProcessOutboxCommand` chạy mỗi phút, process unprocessed events
5. **Event Dispatch**: Events được dispatch đến event listeners
6. **Mark Processed**: Events được đánh dấu `processed_at` sau khi dispatch thành công

## Event Listeners

### IdentityAccess Event Listeners

**UserIdentityWasCreated**:
- Send welcome email
- Create user profile (OrganizationalStructure context)
- Invalidate cache

**PasswordWasChanged**:
- Send password change notification email
- Invalidate user sessions (security)

**UserWasAuthenticated**:
- Update last login timestamp
- Log authentication event (audit)

**TokenWasIssued**:
- Log token issuance (audit)
- Update token statistics

**TokenWasRevoked**:
- Log token revocation (audit)
- Invalidate cache

### OrganizationalStructure Event Listeners

**UserWasCreated**:
- Send profile creation notification
- Update user statistics

**UserWasAssignedToFaculty**:
- Update faculty user count
- Send notification to faculty admin

**FacultyWasCreated**:
- Initialize faculty resources
- Send notification to system admin

## Rationale

### Tại sao chọn Event-Driven Architecture?

1. **Decoupling**: Contexts không cần biết về implementation của context khác
2. **Scalability**: Có thể scale event processing độc lập
3. **Flexibility**: Dễ dàng thêm side effects mà không thay đổi core logic
4. **Reliability**: Outbox Pattern đảm bảo events không bị mất

### Tại sao chọn Outbox Pattern?

1. **Reliability**: Events được lưu trong cùng transaction với business data
2. **Simplicity**: Không cần external message queue (có thể migrate sau nếu cần)
3. **Exactly-once delivery**: Có thể implement idempotency checks
4. **Laravel Integration**: Dễ integrate với Laravel's queue system

## Consequences

### Positive

- ✅ **Decoupling**: Contexts communicate qua events, không direct calls
- ✅ **Reliability**: Outbox Pattern đảm bảo events không bị mất
- ✅ **Scalability**: Có thể scale event processing độc lập
- ✅ **Flexibility**: Dễ dàng thêm side effects
- ✅ **Testability**: Dễ test với event mocks

### Negative

- ⚠️ **Complexity**: Event-driven architecture adds complexity
- ⚠️ **Eventual Consistency**: Side effects có thể không immediate (eventual consistency)
- ⚠️ **Debugging**: Khó debug khi có nhiều events và listeners
- ⚠️ **Outbox Processing**: Cần background job để process outbox

### Mitigation

- ✅ **Documentation**: Document rõ ràng event flow và listeners
- ✅ **Monitoring**: Monitor outbox processing và event dispatch
- ✅ **Idempotency**: Implement idempotency checks trong event listeners
- ✅ **Testing**: Comprehensive tests cho event flow

## Alternatives Considered

### 1. Synchronous Calls

**Rejected**: 
- Creates tight coupling giữa contexts
- Difficult to scale
- Side effects block main operation

### 2. Message Queue (RabbitMQ, Kafka)

**Rejected**:
- Adds external dependency
- Over-engineering cho hệ thống hiện tại
- Outbox Pattern đơn giản hơn và đủ cho requirements
- Có thể migrate sang message queue sau nếu cần scale

### 3. Event Sourcing

**Rejected**:
- Event Sourcing phức tạp hơn và yêu cầu thay đổi lớn trong architecture
- Outbox Pattern đủ để giải quyết vấn đề hiện tại
- Có thể migrate sang Event Sourcing sau nếu cần

## Implementation Notes

### Event Recording

Aggregates record events internally:

```php
final class UserIdentity
{
    private array $domainEvents = [];

    public function changePassword(PasswordHash $newPassword): void
    {
        $this->passwordHash = $newPassword;
        $this->recordDomainEvent(new PasswordWasChanged(
            $this->id->toString(),
            (new \DateTimeImmutable()),
        ));
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

### Outbox Storage

Use Cases store events vào outbox:

```php
final class ChangePasswordUseCase
{
    public function execute(ChangePasswordDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $userIdentity = $this->userIdentityRepository->findById(...);
            $userIdentity->changePassword(...);
            $this->userIdentityRepository->save($userIdentity);

            // Store events in outbox
            foreach ($userIdentity->pullDomainEvents() as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'user_identity',
                    $userIdentity->id()->toString(),
                    $event::class,
                    $event->toPayload(),
                );
            }
        });
    }
}
```

### Event Processing

Background command processes outbox:

```php
final class ProcessOutboxCommand extends Command
{
    public function handle(): void
    {
        $events = $this->outboxEventRepository->getUnprocessed(100);

        foreach ($events as $event) {
            try {
                $domainEvent = $this->eventFactory->create($event);
                $this->eventDispatcher->dispatch($domainEvent);
                $this->outboxEventRepository->markAsProcessed($event->id);
            } catch (\Throwable $e) {
                Log::error('Failed to process outbox event', [
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
```

## References

- [Domain Events](https://martinfowler.com/eaaDev/DomainEvent.html)
- [Transactional Outbox Pattern](https://microservices.io/patterns/data/transactional-outbox.html)
- [Event-Driven Architecture](https://martinfowler.com/articles/201701-event-driven.html)
