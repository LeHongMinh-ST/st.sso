# Quy ước phát triển dự án (Development Conventions)

Tài liệu này định nghĩa các quy ước và tiêu chuẩn bắt buộc khi phát triển dự án, nhằm đảm bảo chất lượng mã nguồn, tính nhất quán và tuân thủ chặt chẽ kiến trúc Domain-Driven Design (DDD) đã đề ra.

## 1. Quy ước về Typing (Strict Typing)

Mục tiêu là tối đa hóa sự chặt chẽ của mã nguồn PHP, giảm thiểu lỗi và tăng cường khả năng phân tích tĩnh.

1.  **Bật Strict Types:** Mọi tệp tin PHP **BẮT BUỘC** phải bắt đầu bằng `declare(strict_types=1);`.
2.  **Khai báo kiểu cho tham số (Parameters):** Mọi tham số của hàm/phương thức phải được khai báo kiểu dữ liệu rõ ràng.
3.  **Khai báo kiểu trả về (Return Types):** Mọi hàm/phương thức phải khai báo kiểu dữ liệu trả về. Nếu một hàm không trả về gì, nó phải được khai báo là `void`.
4.  **Khai báo kiểu cho thuộc tính (Properties):** Mọi thuộc tính của lớp (class properties) phải được khai báo kiểu dữ liệu.

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;

final class FindUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(string $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }
}
```

## 2. Quy ước về Documentation và Code Style

1.  **PHPDoc Blocks:** Mọi phương thức (public, protected, private) trong các lớp **BẮT BUỘC** phải có PHPDoc block đầy đủ bằng tiếng anh, mô tả rõ ràng:
    *   Mục đích của phương thức.
    *   Mô tả cho từng tham số (`@param`).
    *   Mô tả cho giá trị trả về (`@return`).
    *   Các exception có thể được ném ra (`@throws`).

2.  **Tuân thủ Code Style:**
    *   Toàn bộ mã nguồn phải được định dạng tự động bằng **Laravel Pint**.
    *   Mã nguồn phải tuân thủ các quy tắc được định nghĩa trong bộ quy tắc của **PHP_CodeSniffer (phpcs)**.
    *   **Khuyến khích:** Tích hợp việc chạy `pint` và `phpcs` vào Git pre-commit hook để đảm bảo chất lượng mã nguồn trước khi commit.

## 3. Quy ước về Kiến trúc (Architectural Conventions)

### 3.1. Giao tiếp giữa các Bounded Contexts

*   **NGHIÊM CẤM** việc gọi trực tiếp đến các lớp Domain hoặc Infrastructure của một Bounded Context (BC) khác. Ví dụ: một Use Case trong `IdentityAccess` không được phép gọi trực tiếp đến một Repository của `OrganizationalStructure`.
*   Mọi giao tiếp giữa các BC phải được thực hiện thông qua **"cổng" (Port)** là lớp **Application** của BC đích.
*   Có hai cách giao tiếp chính:
    1.  **Đồng bộ (Synchronous):** Một Use Case trong BC-A có thể gọi một Use Case khác trong BC-B. Cách này chỉ nên dùng khi kết quả của BC-B là cần thiết ngay lập tức cho logic của BC-A.
    2.  **Bất đồng bộ (Asynchronous):** Sử dụng Events (xem mục 3.2). Đây là cách được **ƯU TIÊN** để đảm bảo hệ thống được découple tối đa.

### 3.2. Xử lý logic liên quan qua Events (Loose Coupling)

*   Khi một hành động trong một BC có thể gây ra một hoặc nhiều tác vụ phụ (side effects) ở các BC khác, và các tác vụ đó không cần phải thực thi ngay lập tức trong cùng một transaction, chúng ta **BẮT BUỘC** phải sử dụng **Domain Events**.
*   **Ví dụ:** Khi một `User` mới được tạo (trong `OrganizationalStructureContext`), aggregate `User` sẽ tạo ra một event `UserWasCreated`.
*   Các BC khác (ví dụ `IdentityAccessContext` hoặc `NotificationContext`) sẽ có các **Listeners** lắng nghe event này và thực hiện các hành động tương ứng (ví dụ: tạo thông tin đăng nhập mặc định, gửi email chào mừng).
*   Điều này giúp cho `OrganizationalStructureContext` không cần biết bất cứ điều gì về logic gửi email hay tạo thông tin đăng nhập.

## 4. Quy ước về Exception Handling

### 4.1. Phân loại Exception

Hệ thống sử dụng các loại exception khác nhau để xử lý các tình huống khác nhau:

1. **Domain Exceptions:**
   - Được định nghĩa trong Domain Layer
   - Đại diện cho các lỗi nghiệp vụ
   - Ví dụ: `UserNotFoundException`, `InvalidEmailException`, `InsufficientPermissionException`
   - Nên extend từ `DomainException` hoặc các exception cụ thể hơn

2. **Application Exceptions:**
   - Được định nghĩa trong Application Layer
   - Đại diện cho các lỗi trong quá trình xử lý use case
   - Ví dụ: `UserAlreadyExistsException`, `InvalidCredentialsException`

3. **Infrastructure Exceptions:**
   - Được định nghĩa trong Infrastructure Layer
   - Đại diện cho các lỗi kỹ thuật
   - Ví dụ: `DatabaseConnectionException`, `ExternalServiceUnavailableException`

### 4.2. Quy ước xử lý Exception

- **Trong Domain Layer:**
  - Throw Domain Exceptions khi vi phạm business rules
  - Không catch exceptions từ Infrastructure layer
  - Exception messages nên rõ ràng và có ý nghĩa nghiệp vụ

- **Trong Application Layer:**
  - Catch Domain Exceptions và có thể wrap thành Application Exceptions nếu cần
  - Log exceptions để debug
  - Không expose chi tiết kỹ thuật ra ngoài

- **Trong Infrastructure Layer (Controllers):**
  - Catch Application/Domain Exceptions và convert thành HTTP responses phù hợp
  - Return appropriate HTTP status codes (400, 404, 500, etc.)
  - Log exceptions với đầy đủ context

**Ví dụ:**
```php
// Domain Layer
if (!$user) {
    throw new UserNotFoundException("User with ID {$userId} not found");
}

// Application Layer
try {
    $user = $this->userRepository->findById($userId);
    if (!$user) {
        throw new UserNotFoundException();
    }
} catch (UserNotFoundException $e) {
    \Log::error("User not found", ['user_id' => $userId]);
    throw $e; // Re-throw để Controller xử lý
}

// Infrastructure Layer (Controller)
try {
    $this->useCase->handle($dto);
    return response()->json(['message' => 'Success']);
} catch (UserNotFoundException $e) {
    return response()->json(['error' => 'User not found'], 404);
} catch (\Exception $e) {
    \Log::error("Unexpected error", ['exception' => $e]);
    return response()->json(['error' => 'An error occurred'], 500);
}
```

## 5. Quy ước về Testing

### 5.1. Phân loại Test

1. **Unit Tests:**
   - Test các thành phần độc lập (Domain Objects, Value Objects)
   - Không có dependency vào database hoặc framework
   - Nhanh và chạy thường xuyên
   - Vị trí: `tests/Unit/`

2. **Feature Tests:**
   - Test các use cases từ đầu đến cuối
   - Có thể có dependency vào database (sử dụng transactions)
   - Test các integration giữa các layer
   - Vị trí: `tests/Feature/`

3. **Integration Tests:**
   - Test tích hợp với các services bên ngoài
   - Test API endpoints
   - Có thể sử dụng test database
   - Vị trí: `tests/Integration/`

### 5.2. Quy ước viết Test

- **Naming Convention:**
  - Test method names nên mô tả rõ ràng điều gì đang được test
  - Format: `test_{what}_when_{condition}_then_{expected_result}`
  - Ví dụ: `test_user_cannot_login_when_password_is_incorrect_then_throws_exception`

- **Test Structure (AAA Pattern):**
  - **Arrange:** Setup dữ liệu và dependencies
  - **Act:** Thực hiện hành động cần test
  - **Assert:** Kiểm tra kết quả

- **Best Practices:**
  - Mỗi test chỉ test một điều
  - Test nên độc lập với nhau
  - Sử dụng factories và seeders để tạo test data
  - Mock external services
  - Clean up sau mỗi test

**Ví dụ:**
```php
public function test_user_cannot_be_created_with_invalid_email(): void
{
    // Arrange
    $invalidEmail = 'not-an-email';

    // Act & Assert
    $this->expectException(InvalidArgumentException::class);
    Email::fromString($invalidEmail);
}
```

## 6. Quy ước về Version Control

### 6.1. Commit Messages

- Sử dụng format: `type(scope): description`
- Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`
- Scope: Tên của Bounded Context hoặc module (ví dụ: `IdentityAccess`, `OrganizationalStructure`)
- Description: Mô tả ngắn gọn về thay đổi

**Ví dụ:**
- `feat(IdentityAccess): add Microsoft Azure authentication`
- `fix(OrganizationalStructure): fix user import validation`
- `refactor(SharedKernel): extract Email value object`

### 6.2. Branch Naming

- `main` hoặc `master`: Branch chính, luôn stable
- `develop`: Branch phát triển
- `feature/{feature-name}`: Feature branches
- `fix/{bug-name}`: Bug fix branches
- `refactor/{component-name}`: Refactoring branches

**Ví dụ:**
- `feature/IdentityAccess/microsoft-auth`
- `fix/OrganizationalStructure/user-import`
- `refactor/SharedKernel/value-objects`

## 7. Kiến trúc xử lý Event giữa các Context

Để đảm bảo tính nhất quán và cung cấp các ví dụ mẫu chi tiết, toàn bộ kiến trúc xử lý sự kiện (event-driven architecture), bao gồm luồng xử lý, mẫu file và ví dụ mã nguồn cho từng thành phần (Event, Dispatcher, Listener), được định nghĩa tập trung tại tài liệu kiến trúc chính.

**Vui lòng tham khảo mục "5. Kiến trúc Event-Driven với Transactional Outbox Pattern" trong tệp tin `.ai-knowledge/commons/02-architecture.md`** để biết thông tin chi tiết.
