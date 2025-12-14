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
  - Return appropriate HTTP status codes theo bảng mapping (xem 4.3)
  - Log exceptions với đầy đủ context
  - **BẮT BUỘC**: Mọi exception phải được map đúng HTTP status code

### 4.3. Bảng Mapping Exception -> HTTP Status Code

**BẮT BUỘC**: Mọi exception phải được map đúng HTTP status code theo bảng sau:

| Exception Type | HTTP Status Code | Mô tả | Ví dụ |
|----------------|------------------|-------|-------|
| **EntityNotFoundException** | `404 Not Found` | Entity không tồn tại | `UserNotFoundException`, `FacultyNotFoundException` |
| **UserAlreadyExistsException** | `409 Conflict` | Resource đã tồn tại | User với email/username đã tồn tại |
| **InvalidArgumentException** | `400 Bad Request` | Dữ liệu đầu vào không hợp lệ | Email format sai, UUID không hợp lệ |
| **DomainException** (generic) | `400 Bad Request` | Lỗi nghiệp vụ chung | Business rule violation |
| **ValidationException** (Laravel) | `422 Unprocessable Entity` | Validation errors | Form Request validation failed |
| **AuthenticationException** | `401 Unauthorized` | Chưa authenticate | Token không hợp lệ hoặc thiếu |
| **AuthorizationException** | `403 Forbidden` | Không có quyền | User không có permission |
| **ModelNotFoundException** (Laravel) | `404 Not Found` | Model không tồn tại | Eloquent model not found |
| **QueryException** (Database) | `500 Internal Server Error` | Lỗi database | SQL syntax error, connection error |
| **Exception** (generic) | `500 Internal Server Error` | Lỗi server không xác định | Unexpected errors |

### 4.4. Chi tiết Mapping cho từng Exception

#### 4.4.1. EntityNotFoundException (404 Not Found)

**Exceptions:**
- `UserNotFoundException`
- `FacultyNotFoundException`
- `DepartmentNotFoundException`
- Bất kỳ exception nào extends `EntityNotFoundException`

**Response Format:**
```json
{
    "message": "User not found",
    "errors": {
        "id": ["User with ID {uuid} not found"]
    }
}
```

**Ví dụ Controller:**
```php
try {
    $user = $this->findUserUseCase->execute($id);
    return new UserResource($user);
} catch (UserNotFoundException $e) {
    return response()->json([
        'message' => 'User not found',
    ], 404);
}
```

#### 4.4.2. UserAlreadyExistsException (409 Conflict)

**Response Format:**
```json
{
    "message": "User already exists",
    "errors": {
        "email": ["User with email {email} already exists"]
    }
}
```

**Ví dụ Controller:**
```php
try {
    $user = $this->createUserUseCase->execute($dto);
    return (new UserResource($user))->response()->setStatusCode(201);
} catch (UserAlreadyExistsException $e) {
    return response()->json([
        'message' => 'User already exists',
        'errors' => [
            'email' => [$e->getMessage()],
        ],
    ], 409);
}
```

#### 4.4.3. InvalidArgumentException (400 Bad Request)

**Response Format:**
```json
{
    "message": "Invalid input data",
    "errors": {
        "field": ["Error message"]
    }
}
```

**Ví dụ Controller:**
```php
try {
    $email = Email::fromString($request->input('email'));
} catch (InvalidArgumentException $e) {
    return response()->json([
        'message' => 'Invalid input data',
        'errors' => [
            'email' => [$e->getMessage()],
        ],
    ], 400);
}
```

#### 4.4.4. ValidationException (422 Unprocessable Entity)

**Response Format:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field1": ["The field1 field is required."],
        "field2": ["The field2 must be a valid email address."]
    }
}
```

**Note**: Laravel tự động handle ValidationException từ Form Requests, không cần catch trong Controller.

#### 4.4.5. AuthenticationException (401 Unauthorized)

**Response Format:**
```json
{
    "message": "Unauthenticated."
}
```

**Note**: Laravel tự động handle AuthenticationException qua middleware.

#### 4.4.6. AuthorizationException (403 Forbidden)

**Response Format:**
```json
{
    "message": "This action is unauthorized."
}
```

**Ví dụ Controller:**
```php
if (!$this->authorize('create', User::class)) {
    return response()->json([
        'message' => 'This action is unauthorized.',
    ], 403);
}
```

#### 4.4.7. Generic Exception (500 Internal Server Error)

**Response Format:**
```json
{
    "message": "An error occurred while processing your request."
}
```

**Ví dụ Controller:**
```php
try {
    // ... code ...
} catch (\Exception $e) {
    \Log::error('Unexpected error', [
        'exception' => $e,
        'trace' => $e->getTraceAsString(),
    ]);
    
    return response()->json([
        'message' => 'An error occurred while processing your request.',
    ], 500);
}
```

### 4.5. Best Practices cho Exception Handling trong Controllers

1. **Catch Specific Exceptions First**: Luôn catch specific exceptions trước generic Exception
2. **Log Exceptions**: Log tất cả exceptions với đầy đủ context (request data, user info, stack trace)
3. **Don't Expose Internal Details**: Không expose chi tiết kỹ thuật (file paths, stack traces) trong production
4. **Consistent Error Format**: Sử dụng format nhất quán cho tất cả error responses
5. **Use HTTP Status Codes Correctly**: Tuân thủ đúng bảng mapping ở trên

**Ví dụ đầy đủ:**
```php
public function store(CreateUserRequest $request): JsonResponse
{
    try {
        $dto = CreateUserDTO::fromArray($request->validated());
        $user = $this->createUserUseCase->execute($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    } catch (UserAlreadyExistsException $e) {
        // 409 Conflict
        return response()->json([
            'message' => 'User already exists',
            'errors' => [
                'email' => [$e->getMessage()],
            ],
        ], 409);
    } catch (InvalidArgumentException $e) {
        // 400 Bad Request
        return response()->json([
            'message' => 'Invalid input data',
            'errors' => [
                'input' => [$e->getMessage()],
            ],
        ], 400);
    } catch (\Exception $e) {
        // 500 Internal Server Error
        \Log::error('Error creating user', [
            'exception' => $e,
            'request_data' => $request->all(),
        ]);

        return response()->json([
            'message' => 'An error occurred while processing your request.',
        ], 500);
    }
}
```

### 4.6. Exception Handler (Optional - Recommended)

Để tránh lặp lại code trong mỗi controller, có thể tạo Exception Handler hoặc Trait:

```php
// app/OrganizationalStructure/Infrastructure/Http/Concerns/HandlesExceptions.php
trait HandlesExceptions
{
    protected function handleException(\Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof UserNotFoundException => $this->notFoundResponse('User not found'),
            $e instanceof FacultyNotFoundException => $this->notFoundResponse('Faculty not found'),
            $e instanceof DepartmentNotFoundException => $this->notFoundResponse('Department not found'),
            $e instanceof UserAlreadyExistsException => $this->conflictResponse($e->getMessage()),
            $e instanceof InvalidArgumentException => $this->badRequestResponse($e->getMessage()),
            default => $this->serverErrorResponse($e),
        };
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 404);
    }

    private function conflictResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => 'Resource already exists',
            'errors' => ['resource' => [$message]],
        ], 409);
    }

    private function badRequestResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => 'Invalid input data',
            'errors' => ['input' => [$message]],
        ], 400);
    }

    private function serverErrorResponse(\Throwable $e): JsonResponse
    {
        \Log::error('Unexpected error', [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'An error occurred while processing your request.',
        ], 500);
    }
}
```

**Sử dụng trong Controller:**
```php
use HandlesExceptions;

public function store(CreateUserRequest $request): JsonResponse
{
    try {
        $dto = CreateUserDTO::fromArray($request->validated());
        $user = $this->createUserUseCase->execute($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    } catch (\Throwable $e) {
        return $this->handleException($e);
    }
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

## 10. Quy ước về API Design với Laravel Resources

**BẮT BUỘC**: Mọi API endpoint phải tuân thủ RESTful conventions và sử dụng Laravel API Resources để format responses. API phải được thiết kế để phản ánh Aggregate structure, không phải theo chức năng.

### 10.1. Nguyên tắc thiết kế API

1. **API phản ánh Aggregate**: API endpoints phải map trực tiếp với Domain Aggregates, không phải theo chức năng
   - ✅ Đúng: `GET /api/users/{id}`, `POST /api/users`, `PUT /api/users/{id}`
   - ❌ Sai: `POST /api/create-user`, `POST /api/update-user-profile`, `POST /api/assign-user-to-faculty`

2. **RESTful Conventions**: Tuân thủ đầy đủ RESTful conventions:
   - `GET /api/{resource}` - List resources
   - `GET /api/{resource}/{id}` - Show resource
   - `POST /api/{resource}` - Create resource
   - `PUT /api/{resource}/{id}` - Update resource (full update)
   - `PATCH /api/{resource}/{id}` - Partial update resource
   - `DELETE /api/{resource}/{id}` - Delete resource

3. **Nested Resources**: Chỉ sử dụng nested resources khi có quan hệ rõ ràng giữa aggregates:
   - ✅ Đúng: `GET /api/faculties/{faculty}/users` (users thuộc về faculty)
   - ❌ Sai: `GET /api/users/{user}/assign-to-faculty` (không phải nested resource)

4. **Actions trên Aggregates**: Các actions phải được thực hiện thông qua update operations:
   - ✅ Đúng: `PATCH /api/users/{id}` với body `{ "faculty_id": "uuid" }` để assign user to faculty
   - ❌ Sai: `POST /api/users/{id}/assign-to-faculty`

### 10.2. Laravel API Resources

**BẮT BUỘC**: Mọi API response phải sử dụng Laravel API Resources để format data.

#### 10.2.1. Cấu trúc Resources

Resources phải được đặt trong `app/{BoundedContext}/Infrastructure/Http/Resources/`:

```
app/
  OrganizationalStructure/
    Infrastructure/
      Http/
        Resources/
          User/
            UserResource.php
            UserCollection.php
          Faculty/
            FacultyResource.php
            FacultyCollection.php
          Department/
            DepartmentResource.php
            DepartmentCollection.php
```

#### 10.2.2. Resource Structure

Mỗi Resource phải:
- Extend `JsonResource`
- Map Domain Aggregate properties sang API response format
- Include relationships khi cần thiết
- Format data theo chuẩn API (snake_case cho keys)

**Ví dụ UserResource**:

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\User;

use App\OrganizationalStructure\Domain\Aggregates\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User API resource.
 * Maps User aggregate to API response format.
 */
final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id()->toString(),
            'user_name' => $user->userName()->toString(),
            'email' => (string) $user->email(),
            'full_name' => $user->fullName()->fullName(),
            'first_name' => $user->fullName()->firstName(),
            'last_name' => $user->fullName()->lastName(),
            'user_code' => $user->userCode()?->toString(),
            'phone' => $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString(),
            'faculty_id' => $user->facultyId()?->toString(),
            'department_id' => $user->departmentId()?->toString(),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at?->toIso8601String()),
            'updated_at' => $this->when($this->updated_at, fn () => $this->updated_at?->toIso8601String()),
        ];
    }
}
```

#### 10.2.3. Resource Collections

Sử dụng Resource Collections cho list endpoints:

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * User collection resource.
 */
final class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
```

### 10.3. Form Requests cho Validation

**BẮT BUỘC**: Mọi API endpoint nhận input phải sử dụng Form Requests để validate.

#### 10.3.1. Cấu trúc Form Requests

Form Requests phải được đặt trong `app/{BoundedContext}/Infrastructure/Http/Requests/`:

```
app/
  OrganizationalStructure/
    Infrastructure/
      Http/
        Requests/
          CreateUserRequest.php
          UpdateUserRequest.php
          AssignUserToFacultyRequest.php
          CreateFacultyRequest.php
          UpdateFacultyRequest.php
```

#### 10.3.2. Form Request Structure

Mỗi Form Request phải:
- Extend `Illuminate\Foundation\Http\FormRequest`
- Implement `rules()` method với validation rules
- Implement `authorize()` method để check permissions
- Map validated data sang DTO format

**Ví dụ CreateUserRequest**:

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a user.
 */
final class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Use policies to check authorization
        return $this->user()->can('create', \App\Models\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_code' => ['nullable', 'string', 'max:50', 'unique:users,code'],
            'phone' => ['nullable', 'string', 'max:20'],
            'faculty_id' => ['nullable', 'uuid', 'exists:faculties,uuid'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,uuid'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_name.unique' => 'Username already exists',
            'email.unique' => 'Email already exists',
            'user_code.unique' => 'User code already exists',
            'faculty_id.exists' => 'Faculty not found',
            'department_id.exists' => 'Department not found',
        ];
    }
}
```

### 10.4. Controller Structure

Controllers phải:
- Sử dụng Form Requests cho validation
- Sử dụng API Resources cho responses
- Delegate business logic to Use Cases
- Return proper HTTP status codes
- Handle exceptions properly

**Ví dụ UserController**:

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Requests\UpdateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * User API controller.
 * Handles HTTP requests for User aggregate.
 */
final class UserController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly FindUserUseCase $findUserUseCase,
        private readonly UpdateUserProfileUseCase $updateUserProfileUseCase,
    ) {
    }

    /**
     * Display a listing of users.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        // Implementation for listing users
        // ...
    }

    /**
     * Store a newly created user.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = CreateUserDTO::fromArray($request->validated());
        $user = $this->createUserUseCase->execute($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified user.
     *
     * @param string $id User ID (UUID)
     * @return UserResource|JsonResponse
     */
    public function show(string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->findUserUseCase->execute($id);

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param string $id User ID (UUID)
     * @return UserResource|JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): UserResource|JsonResponse
    {
        try {
            $dto = UpdateUserProfileDTO::fromArray($request->validated());
            $user = $this->updateUserProfileUseCase->execute($id, $dto);

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }
}
```

### 10.5. Routes Structure

Routes phải follow RESTful conventions và map với aggregates:

```php
// routes/api.php

use App\OrganizationalStructure\Infrastructure\Http\Controllers\UserController;
use App\OrganizationalStructure\Infrastructure\Http\Controllers\FacultyController;
use App\OrganizationalStructure\Infrastructure\Http\Controllers\DepartmentController;

Route::middleware(['auth:api'])->group(function (): void {
    // User aggregate routes
    Route::apiResource('users', UserController::class);
    
    // Faculty aggregate routes
    Route::apiResource('faculties', FacultyController::class);
    
    // Department aggregate routes
    Route::apiResource('departments', DepartmentController::class);
    
    // Nested resources (only when there's a clear aggregate relationship)
    Route::get('faculties/{faculty}/users', [FacultyController::class, 'users'])
        ->name('faculties.users.index');
    Route::get('faculties/{faculty}/departments', [FacultyController::class, 'departments'])
        ->name('faculties.departments.index');
});
```

### 10.6. Response Format

Tất cả API responses phải follow format nhất quán:

**Success Response**:
```json
{
    "data": {
        "id": "uuid",
        "user_name": "john.doe",
        "email": "john@example.com",
        ...
    }
}
```

**Error Response**:
```json
{
    "message": "Error message",
    "errors": {
        "field": ["Error message"]
    }
}
```

**Collection Response**:
```json
{
    "data": [
        { "id": "uuid", ... },
        { "id": "uuid", ... }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### 10.7. Best Practices

1. **Aggregate-First Design**: Luôn thiết kế API theo aggregate structure trước, sau đó map sang use cases
2. **Consistent Naming**: Sử dụng snake_case cho API keys, camelCase cho internal code
3. **Resource Nesting**: Chỉ nest resources khi có quan hệ rõ ràng giữa aggregates
4. **Status Codes**: Sử dụng proper HTTP status codes (200, 201, 204, 400, 404, 422, 500)
5. **Error Handling**: Luôn return consistent error format
6. **Validation**: Validate ở Form Request level, không validate trong Controller
7. **Authorization**: Check permissions trong Form Request `authorize()` method hoặc Policies

### 10.8. Ví dụ đầy đủ

**Ví dụ: Assign User to Faculty**

❌ **SAI** - Thiết kế theo chức năng:
```php
// Route
Route::post('users/{user}/assign-to-faculty', [UserController::class, 'assignToFaculty']);

// Controller
public function assignToFaculty(string $id, Request $request): JsonResponse
{
    // ...
}
```

✅ **ĐÚNG** - Thiết kế theo aggregate:
```php
// Route - Sử dụng PATCH để update user aggregate
Route::patch('users/{user}', [UserController::class, 'update']);

// Form Request
class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'faculty_id' => ['nullable', 'uuid', 'exists:faculties,uuid'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,uuid'],
            // ... other fields
        ];
    }
}

// Controller - Update method xử lý tất cả updates, bao gồm assign to faculty
public function update(UpdateUserRequest $request, string $id): UserResource|JsonResponse
{
    $dto = UpdateUserProfileDTO::fromArray($request->validated());
    // Use Case sẽ handle logic assign to faculty nếu faculty_id được provide
    $user = $this->updateUserProfileUseCase->execute($id, $dto);
    
    return new UserResource($user);
}
```

**Vui lòng tham khảo file `.ai-knowledge/commons/06-api-design-conventions.md`** để biết chi tiết về API design conventions.
