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
**Yêu cầu quan trọng** Yêu cầu các chức năng use case và domain logic đều được test.

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

## 8. Quy ước về API Documentation (Scramble)

**BẮT BUỘC**: Mọi API endpoint phải được document đầy đủ sử dụng Scramble annotations để tự động generate API documentation.

### 8.1. Yêu cầu chung

1. **PHPDoc Blocks**: Mọi API Controller method **BẮT BUỘC** phải có PHPDoc với Scramble annotations
2. **Group Documentation**: Sử dụng `@group` để nhóm các endpoints liên quan
3. **Parameter Documentation**: Document tất cả parameters (query, url, body) với examples
4. **Response Documentation**: Document success và error responses với status codes và structure
5. **Authentication Documentation**: Chỉ định authentication requirement (`@authenticated` hoặc `@unauthenticated`)

### 8.2. Cấu trúc Documentation

```php
/**
 * Method description
 * 
 * @group GroupName
 * 
 * @queryParam param_name type Description. Example: example_value
 * @urlParam param_name type required Description. Example: example_value
 * @bodyParam param_name type required Description. Example: example_value
 * 
 * @response 200 {
     *   "data": {...}
     * }
     * @response 400 {
     *   "message": "Error message"
     * }
     * 
 * @authenticated
 */
public function methodName(Request $request): JsonResponse
{
    // Implementation
}
```

### 8.3. Best Practices

- ✅ Luôn cung cấp examples cho mọi parameter
- ✅ Document tất cả error responses có thể xảy ra
- ✅ Response structure phải match với API Resource structure
- ✅ Sử dụng consistent naming cho parameters
- ✅ Group related endpoints lại với nhau

**Vui lòng tham khảo file `.ai-knowledge/commons/05-api-documentation-scramble.md`** để biết chi tiết về conventions viết API documentation theo Scramble.

## 9. Quy ước về Dependency Injection trong Livewire Components và Import Classes

### 9.1. Vấn đề

**Livewire Components** và một số **Import Classes** (như `Maatwebsite\Excel` import classes) **KHÔNG HỖ TRỢ** constructor injection như các class thông thường trong Laravel. Khi sử dụng constructor injection trong các class này, Laravel sẽ không thể tự động resolve dependencies.

### 9.2. Giải pháp: Sử dụng `app()` Helper

Thay vì sử dụng constructor injection, chúng ta sử dụng `app()` helper để resolve dependencies khi cần thiết.

### 9.3. Pattern cho Livewire Components

**KHÔNG ĐƯỢC** sử dụng constructor injection trong Livewire components:

```php
// ❌ SAI - Không hoạt động trong Livewire
class Create extends Component
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
    ) {
        parent::__construct();
    }
}
```

**PHẢI** sử dụng private helper methods với `app()`:

```php
// ✅ ĐÚNG - Sử dụng app() helper
class Create extends Component
{
    /**
     * Get CreateUserUseCase instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return CreateUserUseCase
     */
    private function getCreateUserUseCase(): CreateUserUseCase
    {
        return app(CreateUserUseCase::class);
    }

    public function submit()
    {
        // Use the helper method to get Use Case instance
        $user = $this->getCreateUserUseCase()->execute($dto);
    }
}
```

### 9.4. Pattern cho Import Classes

**KHÔNG ĐƯỢC** sử dụng constructor injection cho dependencies trong Import classes:

```php
// ❌ SAI - Không hoạt động với Maatwebsite\Excel
class StudentsImport implements ToCollection
{
    public function __construct(
        int $facultyId,
        int $userId,
        private readonly ImportUsersFromExcelUseCase $importUsersUseCase,
    ) {
    }
}
```

**PHẢI** chỉ nhận các parameters cần thiết trong constructor, và sử dụng `app()` helper cho dependencies:

```php
// ✅ ĐÚNG - Chỉ nhận parameters cần thiết, dùng app() cho dependencies
class StudentsImport implements ToCollection
{
    /**
     * Note: ImportUsersFromExcelUseCase is resolved via app() helper
     * because Maatwebsite\Excel import classes don't support constructor injection properly.
     */
    public function __construct(
        int $facultyId,
        int $userId,
    ) {
        $this->facultyId = $facultyId;
        $this->userId = $userId;
    }

    /**
     * Get ImportUsersFromExcelUseCase instance.
     * Using app() helper because import classes don't support constructor injection.
     *
     * @return ImportUsersFromExcelUseCase
     */
    private function getImportUsersUseCase(): ImportUsersFromExcelUseCase
    {
        return app(ImportUsersFromExcelUseCase::class);
    }

    public function collection(Collection $rows): void
    {
        // Use the helper method to get Use Case instance
        $result = $this->getImportUsersUseCase()->execute($rows, $facultyUuid);
    }
}
```

### 9.5. Best Practices

1. **Naming Convention**: Helper methods nên có prefix `get` và suffix là tên class (ví dụ: `getCreateUserUseCase()`, `getUserRepository()`)

2. **Documentation**: Mọi helper method **BẮT BUỘC** phải có PHPDoc giải thích tại sao không dùng constructor injection

3. **Caching**: Không cần cache instances vì Laravel's service container đã handle singleton/transient instances

4. **Type Safety**: Luôn khai báo return type cho helper methods để đảm bảo type safety

5. **Single Responsibility**: Mỗi helper method chỉ resolve một dependency

### 9.6. Ví dụ đầy đủ

```php
<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Application\UseCases\AssignUserToFacultyUseCase;
use Livewire\Component;

class Create extends Component
{
    /**
     * Get CreateUserUseCase instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return CreateUserUseCase
     */
    private function getCreateUserUseCase(): CreateUserUseCase
    {
        return app(CreateUserUseCase::class);
    }

    /**
     * Get AssignUserToFacultyUseCase instance.
     *
     * @return AssignUserToFacultyUseCase
     */
    private function getAssignUserToFacultyUseCase(): AssignUserToFacultyUseCase
    {
        return app(AssignUserToFacultyUseCase::class);
    }

    public function submit()
    {
        // Use helper methods to get Use Case instances
        $user = $this->getCreateUserUseCase()->execute($dto);
        $this->getAssignUserToFacultyUseCase()->execute($userId, $facultyId);
    }
}
```

### 9.7. Lưu ý

- **Controllers**: Vẫn sử dụng constructor injection bình thường vì Laravel hỗ trợ đầy đủ
- **Jobs**: Có thể sử dụng constructor injection cho dependencies, nhưng không thể inject vào `handle()` method parameters nếu class cần constructor parameters
- **Service Providers**: Sử dụng constructor injection bình thường
- **Use Cases**: Sử dụng constructor injection bình thường
