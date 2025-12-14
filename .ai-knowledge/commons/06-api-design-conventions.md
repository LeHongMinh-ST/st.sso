# API Design Conventions - Laravel Resources và RESTful

**Version**: 1.0  
**Last Updated**: 2024-12-14

## Tổng quan

Tài liệu này định nghĩa các quy ước thiết kế API sử dụng Laravel API Resources và tuân thủ RESTful conventions. API phải được thiết kế để phản ánh Aggregate structure, không phải theo chức năng.

## Nguyên tắc cốt lõi

### 1. API phản ánh Aggregate

API endpoints phải map trực tiếp với Domain Aggregates:

- ✅ **Đúng**: `GET /api/users/{id}`, `POST /api/users`, `PUT /api/users/{id}`
- ❌ **Sai**: `POST /api/create-user`, `POST /api/update-user-profile`, `POST /api/assign-user-to-faculty`

### 2. RESTful Conventions

Tuân thủ đầy đủ RESTful conventions:

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| `GET` | `/api/{resource}` | List resources |
| `GET` | `/api/{resource}/{id}` | Show resource |
| `POST` | `/api/{resource}` | Create resource |
| `PUT` | `/api/{resource}/{id}` | Full update resource |
| `PATCH` | `/api/{resource}/{id}` | Partial update resource |
| `DELETE` | `/api/{resource}/{id}` | Delete resource |

### 3. Nested Resources

Chỉ sử dụng nested resources khi có quan hệ rõ ràng giữa aggregates:

- ✅ **Đúng**: `GET /api/faculties/{faculty}/users` (users thuộc về faculty)
- ❌ **Sai**: `GET /api/users/{user}/assign-to-faculty` (không phải nested resource)

### 4. Actions trên Aggregates

Các actions phải được thực hiện thông qua update operations:

- ✅ **Đúng**: `PATCH /api/users/{id}` với body `{ "faculty_id": "uuid" }` để assign user to faculty
- ❌ **Sai**: `POST /api/users/{id}/assign-to-faculty`

## Cấu trúc Files

### Resources Structure

```
app/
  {BoundedContext}/
    Infrastructure/
      Http/
        Resources/
          {Aggregate}/
            {Aggregate}Resource.php
            {Aggregate}Collection.php
```

**Ví dụ**:
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
```

### Form Requests Structure

```
app/
  {BoundedContext}/
    Infrastructure/
      Http/
        Requests/
          Create{Aggregate}Request.php
          Update{Aggregate}Request.php
```

**Ví dụ**:
```
app/
  OrganizationalStructure/
    Infrastructure/
      Http/
        Requests/
          CreateUserRequest.php
          UpdateUserRequest.php
          CreateFacultyRequest.php
          UpdateFacultyRequest.php
```

## API Resource Implementation

### Resource Class Template

```php
<?php

declare(strict_types=1);

namespace App\{BoundedContext}\Infrastructure\Http\Resources\{Aggregate};

use App\{BoundedContext}\Domain\Aggregates\{Aggregate};
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * {Aggregate} API resource.
 * Maps {Aggregate} aggregate to API response format.
 */
final class {Aggregate}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var {Aggregate} ${aggregate} */
        ${aggregate} = $this->resource;

        return [
            'id' => ${aggregate}->id()->toString(),
            // Map all aggregate properties
            'created_at' => $this->when($this->created_at, fn () => $this->created_at?->toIso8601String()),
            'updated_at' => $this->when($this->updated_at, fn () => $this->updated_at?->toIso8601String()),
        ];
    }
}
```

### Resource Collection Template

```php
<?php

declare(strict_types=1);

namespace App\{BoundedContext}\Infrastructure\Http\Resources\{Aggregate};

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * {Aggregate} collection resource.
 */
final class {Aggregate}Collection extends ResourceCollection
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

## Form Request Implementation

### Form Request Template

```php
<?php

declare(strict_types=1);

namespace App\{BoundedContext}\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a {aggregate}.
 */
final class Create{Aggregate}Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\{Aggregate}::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            // Define validation rules
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
            // Custom error messages
        ];
    }
}
```

## Controller Implementation

### Controller Template

```php
<?php

declare(strict_types=1);

namespace App\{BoundedContext}\Infrastructure\Http\Controllers;

use App\{BoundedContext}\Application\UseCases\{UseCase};
use App\{BoundedContext}\Infrastructure\Http\Requests\{Request};
use App\{BoundedContext}\Infrastructure\Http\Resources\{Aggregate}\{Aggregate}Resource;
use Illuminate\Http\JsonResponse;

/**
 * {Aggregate} API controller.
 * Handles HTTP requests for {Aggregate} aggregate.
 */
final class {Aggregate}Controller
{
    public function __construct(
        private readonly {UseCase} ${useCase},
    ) {
    }

    /**
     * Store a newly created {aggregate}.
     *
     * @param {Request} $request
     * @return JsonResponse
     */
    public function store({Request} $request): JsonResponse
    {
        $dto = {DTO}::fromArray($request->validated());
        ${aggregate} = $this->{useCase}->execute($dto);

        return (new {Aggregate}Resource(${aggregate}))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified {aggregate}.
     *
     * @param string $id {Aggregate} ID (UUID)
     * @return {Aggregate}Resource|JsonResponse
     */
    public function show(string $id): {Aggregate}Resource|JsonResponse
    {
        try {
            ${aggregate} = $this->{useCase}->execute($id);

            return new {Aggregate}Resource(${aggregate});
        } catch ({Exception} $e) {
            return response()->json([
                'message' => '{Aggregate} not found',
            ], 404);
        }
    }
}
```

## Routes Structure

### Routes Template

```php
// routes/api.php

use App\{BoundedContext}\Infrastructure\Http\Controllers\{Aggregate}Controller;

Route::middleware(['auth:api'])->group(function (): void {
    // {Aggregate} aggregate routes
    Route::apiResource('{aggregates}', {Aggregate}Controller::class);
    
    // Nested resources (only when there's a clear aggregate relationship)
    Route::get('{parent}/{parentId}/{children}', [{Parent}Controller::class, '{children}'])
        ->name('{parents}.{children}.index');
});
```

## Response Format Standards

### Success Response (Single Resource)

```json
{
    "data": {
        "id": "uuid",
        "field1": "value1",
        "field2": "value2"
    }
}
```

### Success Response (Collection)

```json
{
    "data": [
        {
            "id": "uuid",
            "field1": "value1"
        },
        {
            "id": "uuid",
            "field1": "value2"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7
    },
    "links": {
        "first": "http://api.example.com/api/users?page=1",
        "last": "http://api.example.com/api/users?page=7",
        "prev": null,
        "next": "http://api.example.com/api/users?page=2"
    }
}
```

### Error Response (Validation)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field1": [
            "The field1 field is required."
        ],
        "field2": [
            "The field2 must be a valid email address."
        ]
    }
}
```

### Error Response (Not Found)

```json
{
    "message": "Resource not found"
}
```

### Error Response (Server Error)

```json
{
    "message": "An error occurred while processing your request."
}
```

## HTTP Status Codes

| Status Code | Usage |
|------------|-------|
| `200 OK` | Successful GET, PUT, PATCH requests |
| `201 Created` | Successful POST requests |
| `204 No Content` | Successful DELETE requests |
| `400 Bad Request` | Invalid request format |
| `401 Unauthorized` | Authentication required |
| `403 Forbidden` | Insufficient permissions |
| `404 Not Found` | Resource not found |
| `422 Unprocessable Entity` | Validation errors |
| `500 Internal Server Error` | Server errors |

## Best Practices

### 1. Aggregate-First Design

Luôn thiết kế API theo aggregate structure trước, sau đó map sang use cases:

```php
// ✅ Đúng: API endpoint phản ánh aggregate
PATCH /api/users/{id}
Body: { "faculty_id": "uuid" }

// ❌ Sai: API endpoint theo chức năng
POST /api/users/{id}/assign-to-faculty
```

### 2. Consistent Naming

- **API Keys**: snake_case (`user_name`, `faculty_id`)
- **Internal Code**: camelCase (`userName`, `facultyId`)
- **Database Columns**: snake_case (`user_name`, `faculty_id`)

### 3. Resource Nesting

Chỉ nest resources khi có quan hệ rõ ràng:

```php
// ✅ Đúng: Users thuộc về Faculty
GET /api/faculties/{faculty}/users

// ❌ Sai: Assign không phải nested resource
POST /api/users/{user}/assign-to-faculty
```

### 4. Validation

- Validate ở Form Request level
- Không validate trong Controller
- Return 422 status code cho validation errors

### 5. Authorization

- Check permissions trong Form Request `authorize()` method
- Hoặc sử dụng Policies
- Return 403 status code cho authorization errors

### 6. Error Handling

- Catch Domain Exceptions trong Controller
- Return proper HTTP status codes
- Return consistent error format
- Log errors với đầy đủ context

## Ví dụ đầy đủ: User Aggregate API

### 1. UserResource

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\User;

use App\OrganizationalStructure\Domain\Aggregates\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
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
        ];
    }
}
```

### 2. CreateUserRequest

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

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
}
```

### 3. UserController

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;

final class UserController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly FindUserUseCase $findUserUseCase,
    ) {
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = CreateUserDTO::fromArray($request->validated());
        $user = $this->createUserUseCase->execute($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

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
}
```

### 4. Routes

```php
Route::middleware(['auth:api'])->group(function (): void {
    Route::apiResource('users', UserController::class);
});
```

## Checklist cho mỗi API Endpoint

- [ ] API endpoint phản ánh aggregate structure
- [ ] Sử dụng Laravel API Resource để format response
- [ ] Sử dụng Form Request để validate input
- [ ] Return proper HTTP status codes
- [ ] Handle exceptions properly
- [ ] Follow RESTful conventions
- [ ] Document với Scramble annotations
- [ ] Test với feature tests

## Tài liệu tham khảo

- [Laravel API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Laravel Form Requests](https://laravel.com/docs/11.x/validation#form-request-validation)
- [RESTful API Design](https://restfulapi.net/)
- [HTTP Status Codes](https://httpstatuses.com/)
