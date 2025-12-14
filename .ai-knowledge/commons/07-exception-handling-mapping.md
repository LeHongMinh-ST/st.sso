# Exception Handling Mapping Table

**Version**: 1.0  
**Last Updated**: 2024-12-14

## Tổng quan

Tài liệu này định nghĩa bảng mapping giữa các Exception types và HTTP Status Codes. **BẮT BUỘC** tuân thủ mapping này trong tất cả Controllers.

## Bảng Mapping Exception -> HTTP Status Code

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

## Chi tiết từng Exception Type

### 1. EntityNotFoundException → 404 Not Found

**Exceptions:**
- `UserNotFoundException`
- `FacultyNotFoundException`
- `DepartmentNotFoundException`
- Bất kỳ exception nào extends `EntityNotFoundException`

**Response Format:**
```json
{
    "message": "User not found"
}
```

**Controller Example:**
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

---

### 2. UserAlreadyExistsException → 409 Conflict

**Response Format:**
```json
{
    "message": "User already exists",
    "errors": {
        "email": ["User with email {email} already exists"]
    }
}
```

**Controller Example:**
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

---

### 3. InvalidArgumentException → 400 Bad Request

**Response Format:**
```json
{
    "message": "Invalid input data",
    "errors": {
        "field": ["Error message"]
    }
}
```

**Controller Example:**
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

---

### 4. ValidationException → 422 Unprocessable Entity

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

---

### 5. AuthenticationException → 401 Unauthorized

**Response Format:**
```json
{
    "message": "Unauthenticated."
}
```

**Note**: Laravel tự động handle AuthenticationException qua middleware.

---

### 6. AuthorizationException → 403 Forbidden

**Response Format:**
```json
{
    "message": "This action is unauthorized."
}
```

**Controller Example:**
```php
if (!$this->authorize('create', User::class)) {
    return response()->json([
        'message' => 'This action is unauthorized.',
    ], 403);
}
```

---

### 7. ModelNotFoundException → 404 Not Found

**Response Format:**
```json
{
    "message": "Resource not found"
}
```

**Note**: Thường được handle tự động bởi Laravel, nhưng có thể catch nếu cần custom response.

---

### 8. QueryException → 500 Internal Server Error

**Response Format:**
```json
{
    "message": "An error occurred while processing your request."
}
```

**Controller Example:**
```php
try {
    // Database operations
} catch (\Illuminate\Database\QueryException $e) {
    \Log::error('Database error', [
        'exception' => $e,
        'sql' => $e->getSql(),
    ]);

    return response()->json([
        'message' => 'An error occurred while processing your request.',
    ], 500);
}
```

---

### 9. Generic Exception → 500 Internal Server Error

**Response Format:**
```json
{
    "message": "An error occurred while processing your request."
}
```

**Controller Example:**
```php
try {
    // ... code ...
} catch (\Exception $e) {
    \Log::error('Unexpected error', [
        'exception' => $e,
        'trace' => $e->getTraceAsString(),
        'request_data' => $request->all(),
    ]);

    return response()->json([
        'message' => 'An error occurred while processing your request.',
    ], 500);
}
```

## Quick Reference Table

| HTTP Status | Exception Types | Use Case |
|-------------|----------------|----------|
| **400** | `InvalidArgumentException`, `DomainException` | Invalid input, business rule violation |
| **401** | `AuthenticationException` | Not authenticated |
| **403** | `AuthorizationException` | Not authorized |
| **404** | `EntityNotFoundException`, `ModelNotFoundException` | Resource not found |
| **409** | `UserAlreadyExistsException` | Resource conflict |
| **422** | `ValidationException` | Validation failed |
| **500** | `QueryException`, `Exception` | Server errors |

## Best Practices

1. **Catch Specific Exceptions First**: Luôn catch specific exceptions trước generic Exception
2. **Log All Exceptions**: Log tất cả exceptions với đầy đủ context
3. **Don't Expose Internal Details**: Không expose stack traces, file paths trong production
4. **Consistent Error Format**: Sử dụng format nhất quán cho tất cả error responses
5. **Use HTTP Status Codes Correctly**: Tuân thủ đúng bảng mapping

## Example: Complete Controller với Exception Handling

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
use Illuminate\Http\JsonResponse;

final class UserController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
    ) {
    }

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

    public function show(string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->findUserUseCase->execute($id);

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            // 404 Not Found
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            // 500 Internal Server Error
            \Log::error('Error finding user', [
                'exception' => $e,
                'user_id' => $id,
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }
}
```

## Tài liệu tham khảo

- [HTTP Status Codes](https://httpstatuses.com/)
- [Laravel Exception Handling](https://laravel.com/docs/11.x/errors)
- [RESTful API Error Handling](https://restfulapi.net/http-status-codes/)
