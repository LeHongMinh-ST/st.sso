# ADR 001: Shared Kernel Design

**Status**: Accepted  
**Date**: 2024-12-14  
**Deciders**: Architecture Team  
**Context**: Migration từ monolithic Laravel application sang Domain-Driven Design (DDD) architecture

## Context

Trong quá trình migration sang DDD, chúng ta cần xác định các thành phần nào sẽ được chia sẻ giữa các Bounded Contexts. Các thành phần này bao gồm:

- Value Objects cơ bản (Email, UUID, Timestamp)
- Domain Exceptions
- Interfaces cho infrastructure services
- Outbox Pattern implementation

## Decision

Chúng ta sẽ tạo một **Shared Kernel** để chứa các thành phần dùng chung này.

### Shared Kernel Components

1. **Value Objects**:
   - `Email`: Email validation và normalization
   - `Uuid`: UUID generation và validation với Ramsey UUID
   - `Timestamp`: Timestamp với timezone support

2. **Exceptions**:
   - `DomainException`: Base exception cho domain layer
   - `EntityNotFoundException`: Exception khi không tìm thấy entity
   - `InvalidArgumentException`: Exception khi argument không hợp lệ

3. **Interfaces**:
   - `EventDispatcherInterface`: Interface để dispatch domain events
   - `ClockInterface`: Interface để lấy current time (useful cho testing)

4. **Infrastructure**:
   - `LaravelEventDispatcher`: Laravel implementation của EventDispatcherInterface
   - `SystemClock` và `FixedClock`: Clock implementations
   - `OutboxEvent`: Eloquent model cho Outbox Pattern
   - `ProcessOutboxCommand`: Command để process outbox events

## Rationale

### Tại sao chọn Shared Kernel?

1. **Consistency**: Đảm bảo tất cả contexts sử dụng cùng một cách để represent các giá trị cơ bản
2. **Type Safety**: Value Objects đảm bảo type safety và validation
3. **Reusability**: Tránh duplicate code giữa các contexts
4. **Testability**: Interfaces cho phép dễ dàng mock trong tests

### Tại sao không đặt trong từng Context?

- **Email, UUID, Timestamp**: Được sử dụng trong nhiều contexts, không thuộc về một context cụ thể nào
- **Exceptions**: Base exceptions được sử dụng xuyên suốt
- **Interfaces**: Cho phép decoupling và testability

### Tại sao chọn Outbox Pattern?

**Problem**: Khi publish Domain Events, chúng ta cần đảm bảo:
- Events được publish **sau khi** business transaction commit thành công
- Events không bị mất nếu application crash trước khi publish
- Events được publish **exactly once** (không duplicate)

**Solution**: Transactional Outbox Pattern
- Lưu events vào `outbox_events` table trong cùng transaction với business data
- Background job process events từ outbox và dispatch chúng
- Event được đánh dấu `processed_at` sau khi dispatch thành công

**Benefits**:
- ✅ Atomicity: Event và business data được lưu trong cùng transaction
- ✅ Reliability: Events không bị mất nếu application crash
- ✅ Exactly-once delivery: Có thể implement idempotency checks

## Consequences

### Positive

- ✅ Consistent cách sử dụng Value Objects trong toàn bộ application
- ✅ Type safety với Value Objects
- ✅ Dễ test với interfaces
- ✅ Reliable event delivery với Outbox Pattern

### Negative

- ⚠️ Shared Kernel có thể trở thành "God Object" nếu không được quản lý cẩn thận
- ⚠️ Changes trong Shared Kernel có thể ảnh hưởng đến nhiều contexts
- ⚠️ Outbox Pattern adds complexity và requires background job

### Mitigation

- ✅ **Strict boundaries**: Chỉ đặt các thành phần thực sự cần thiết vào Shared Kernel
- ✅ **Versioning**: Có thể version Shared Kernel nếu cần breaking changes
- ✅ **Documentation**: Document rõ ràng cách sử dụng và best practices
- ✅ **Monitoring**: Monitor outbox processing để đảm bảo events được process đúng cách

## Alternatives Considered

### 1. Separate Value Objects trong mỗi Context

**Rejected**: Dẫn đến code duplication và inconsistency

### 2. Shared Database Schema

**Rejected**: Violates DDD principles về Bounded Context isolation

### 3. Event Sourcing thay vì Outbox Pattern

**Rejected**: 
- Event Sourcing phức tạp hơn và yêu cầu thay đổi lớn trong architecture
- Outbox Pattern đủ để giải quyết vấn đề hiện tại
- Có thể migrate sang Event Sourcing sau nếu cần

### 4. Message Queue (RabbitMQ, Kafka) thay vì Outbox Pattern

**Rejected**:
- Adds external dependency
- Outbox Pattern đơn giản hơn và không cần infrastructure bổ sung
- Có thể migrate sang message queue sau nếu cần scale

## Implementation Notes

### Value Objects

- Tất cả Value Objects là `final` và `immutable`
- Validation được thực hiện trong constructor
- Implement `Stringable` interface để dễ sử dụng

### Outbox Pattern

- Events được lưu với `aggregate_type`, `aggregate_id`, `event_type`, và `payload` (JSON)
- `ProcessOutboxCommand` chạy mỗi phút (có thể config)
- Events được process theo thứ tự `created_at`
- Failed events được log nhưng không block processing của events khác

### Service Provider

- `SharedKernelServiceProvider` bind interfaces với implementations
- Đăng ký trong `bootstrap/providers.php`

## References

- [Domain-Driven Design - Shared Kernel](https://martinfowler.com/bliki/SharedKernel.html)
- [Transactional Outbox Pattern](https://microservices.io/patterns/data/transactional-outbox.html)
- [Value Objects in DDD](https://martinfowler.com/bliki/ValueObject.html)
