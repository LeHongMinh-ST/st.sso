# Shared Kernel

Shared Kernel là phần core dùng chung cho tất cả các Bounded Contexts trong hệ thống. Nó chứa các Value Objects, Exceptions, Interfaces và Infrastructure components được sử dụng xuyên suốt toàn bộ application.

## Mục đích

Shared Kernel đảm bảo:
- **Consistency**: Tất cả contexts sử dụng cùng một cách để represent các giá trị cơ bản (Email, UUID, Timestamp)
- **Type Safety**: Value Objects đảm bảo type safety và validation
- **Reusability**: Tránh duplicate code giữa các contexts
- **Testability**: Interfaces cho phép dễ dàng mock trong tests

## Cấu trúc

```
app/SharedKernel/
├── Domain/
│   ├── ValueObjects/
│   │   ├── Email.php
│   │   ├── Uuid.php
│   │   └── Timestamp.php
│   ├── Exceptions/
│   │   ├── DomainException.php
│   │   ├── EntityNotFoundException.php
│   │   └── InvalidArgumentException.php
│   └── Interfaces/
│       ├── EventDispatcherInterface.php
│       └── ClockInterface.php
└── Infrastructure/
    ├── Clock/
    │   ├── SystemClock.php
    │   └── FixedClock.php
    ├── EventDispatcher/
    │   └── LaravelEventDispatcher.php
    ├── Outbox/
    │   └── OutboxEvent.php
    ├── Http/
    │   └── Controllers/
    │       └── HealthCheckController.php
    └── Providers/
        └── SharedKernelServiceProvider.php
```

## Value Objects

### Email

Value Object để represent email address với validation.

**Usage**:
```php
use App\SharedKernel\Domain\ValueObjects\Email;

// Create from string
$email = Email::fromString('user@example.com');

// Get value
echo (string) $email; // 'user@example.com'

// Get domain
echo $email->domain(); // 'example.com'

// Get local part
echo $email->localPart(); // 'user'

// Compare
$email1 = Email::fromString('user@example.com');
$email2 = Email::fromString('user@example.com');
$email1->equals($email2); // true
```

**Validation**:
- Email format được validate bằng `filter_var()`
- Email được normalize thành lowercase
- Whitespace được trim

### Uuid

Value Object để represent UUID với Ramsey UUID library.

**Usage**:
```php
use App\SharedKernel\Domain\ValueObjects\Uuid;

// Create from string
$uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

// Generate new UUID
$newUuid = Uuid::generate();

// Get value
echo (string) $uuid; // '550e8400-e29b-41d4-a716-446655440000'
echo $uuid->toString(); // same as above

// Compare
$uuid1 = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
$uuid2 = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
$uuid1->equals($uuid2); // true
```

### Timestamp

Value Object để represent timestamp với timezone support.

**Usage**:
```php
use App\SharedKernel\Domain\ValueObjects\Timestamp;

// Create from DateTimeImmutable
$timestamp = Timestamp::fromDateTime(new DateTimeImmutable('2024-01-01 12:00:00'));

// Create from string
$timestamp = Timestamp::fromString('2024-01-01T12:00:00+00:00');

// Create for current time
$now = Timestamp::now();

// Get DateTimeImmutable
$dateTime = $timestamp->toDateTime();

// Format
echo $timestamp->format('Y-m-d'); // '2024-01-01'

// Compare
$timestamp1 = Timestamp::fromString('2024-01-01 12:00:00');
$timestamp2 = Timestamp::fromString('2024-01-01 13:00:00');
$timestamp1->isBefore($timestamp2); // true
$timestamp2->isAfter($timestamp1); // true
```

## Exceptions

### DomainException

Base exception cho domain layer. Tất cả domain exceptions nên extend từ class này.

### EntityNotFoundException

Exception được throw khi không tìm thấy entity.

**Usage**:
```php
use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;

// Generic
throw new EntityNotFoundException('User', '123');

// Factory methods
throw EntityNotFoundException::user('123');
throw EntityNotFoundException::role('456');
```

### InvalidArgumentException

Exception được throw khi argument không hợp lệ.

## Interfaces

### EventDispatcherInterface

Interface để dispatch domain events, cho phép dễ dàng mock trong tests.

**Usage**:
```php
use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;

class MyUseCase
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function handle(): void
    {
        $event = new MyDomainEvent();
        $this->eventDispatcher->dispatch($event);
    }
}
```

### ClockInterface

Interface để lấy current time, hữu ích cho testing.

**Usage**:
```php
use App\SharedKernel\Domain\Interfaces\ClockInterface;

class MyUseCase
{
    public function __construct(
        private ClockInterface $clock
    ) {
    }

    public function handle(): void
    {
        $now = $this->clock->now();
        // Use $now in business logic
    }
}
```

## Outbox Pattern

Outbox Pattern được sử dụng để đảm bảo tính nhất quán dữ liệu khi giao tiếp giữa các Bounded Contexts thông qua Domain Events.

### Cách hoạt động

1. Khi một Domain Event được tạo, nó được lưu vào `outbox_events` table trong cùng transaction với business data
2. Một background job (`ProcessOutboxCommand`) sẽ process các events từ outbox và dispatch chúng
3. Sau khi dispatch thành công, event được đánh dấu là `processed_at`

### Usage

**Lưu event vào outbox**:
```php
use Illuminate\Support\Facades\DB;
use App\SharedKernel\Domain\ValueObjects\Uuid;

DB::transaction(function () use ($user, $event) {
    // Save user
    $this->userRepository->save($user);
    
    // Save event to outbox
    DB::table('outbox_events')->insert([
        'id' => Uuid::generate()->toString(),
        'aggregate_type' => 'user',
        'aggregate_id' => $user->id()->toString(),
        'event_type' => get_class($event),
        'payload' => json_encode($event->toPayload()),
        'created_at' => now(),
    ]);
});
```

**Process outbox events**:
```bash
# Manual
php artisan outbox:process

# With limit
php artisan outbox:process --limit=50

# Scheduled (should be configured in scheduler)
# Runs every minute automatically
```

## Health Check Endpoints

### Basic Health Check

```
GET /api/health
```

Returns:
```json
{
    "status": "ok",
    "timestamp": "2024-01-01T12:00:00+00:00",
    "version": "1.0.0"
}
```

### Detailed Health Check

```
GET /api/health/detailed
```

Returns:
```json
{
    "status": "ok",
    "timestamp": "2024-01-01T12:00:00+00:00",
    "version": "1.0.0",
    "checks": {
        "database": {
            "status": "ok",
            "message": "Database connection successful"
        },
        "cache": {
            "status": "ok",
            "message": "Cache connection successful",
            "driver": "redis"
        }
    }
}
```

## Testing

Tất cả Value Objects và Infrastructure components đều có unit tests với coverage >= 90%.

**Run tests**:
```bash
php artisan test tests/Unit/SharedKernel/
```

## Best Practices

1. **Always use Value Objects**: Không sử dụng primitive types (string, int) khi có Value Object tương ứng
2. **Use Interfaces**: Inject interfaces thay vì concrete implementations để dễ test
3. **Immutable**: Tất cả Value Objects là immutable
4. **Validation**: Validation được thực hiện trong constructor của Value Objects
5. **Type Safety**: Sử dụng strict types (`declare(strict_types=1);`)

## Dependencies

- `ramsey/uuid`: UUID generation và validation
- Laravel Framework: Infrastructure implementations

## Service Provider

`SharedKernelServiceProvider` được đăng ký trong `bootstrap/providers.php` và bind các interfaces với implementations:

- `EventDispatcherInterface` → `LaravelEventDispatcher`
- `ClockInterface` → `SystemClock`
