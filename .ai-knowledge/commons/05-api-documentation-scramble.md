# API Documentation Conventions - Scramble

**Tài liệu này định nghĩa các quy ước viết API documentation sử dụng Scramble** (https://scramble.dedoc.co/) - một thư viện Laravel để tự động generate API documentation từ code annotations.

## 1. Tổng quan về Scramble

Scramble là một thư viện Laravel tự động generate API documentation từ code annotations. Nó:
- ✅ Tự động detect routes và controllers
- ✅ Generate documentation từ PHPDoc annotations
- ✅ Support Laravel Request validation rules
- ✅ Support Laravel API Resources
- ✅ Tự động detect authentication requirements
- ✅ Generate interactive API documentation UI

## 2. Cài đặt và Cấu hình

### 2.1. Cài đặt

```bash
composer require dedoc/scramble
php artisan vendor:publish --tag=scramble-config
```

### 2.2. Cấu hình

File `config/scramble.php`:

```php
<?php

return [
    'api' => [
        'info' => [
            'title' => 'SSO API Documentation',
            'version' => '1.0.0',
            'description' => 'API documentation for Single Sign-On system',
        ],
        'servers' => [
            [
                'url' => env('APP_URL', 'http://localhost'),
                'description' => 'Local development server',
            ],
        ],
    ],
    'middleware' => [
        'web', // Add middleware if needed
    ],
    'routes' => [
        'api', // Scan routes/api.php
    ],
];
```

### 2.3. Routes

Thêm route để access documentation:

```php
// routes/web.php hoặc routes/api.php
Route::get('/api-docs', function () {
    return view('scramble::docs');
})->middleware('web');
```

Hoặc sử dụng route mặc định của Scramble (nếu có).

## 3. Conventions viết API Documentation

### 3.1. Controller Documentation

**BẮT BUỘC**: Mọi API Controller method phải có PHPDoc với Scramble annotations.

#### 3.1.1. Basic Structure

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Users
 * 
 * APIs for managing users
 */
class UserController extends Controller
{
    /**
     * Get list of users
     * 
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page. Example: 15
     * @queryParam search string Search term. Example: john
     * @queryParam faculty_ids array List of faculty IDs. Example: [1, 2]
     * @queryParam roles array List of roles. Example: ["student", "teacher"]
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_name": "john.doe",
     *       "first_name": "John",
     *       "last_name": "Doe",
     *       "email": "john.doe@example.com"
     *     }
     *   ],
     *   "current_page": 1,
     *   "per_page": 15,
     *   "total": 100
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        // Implementation
    }
}
```

#### 3.1.2. Show Method (Single Resource)

```php
/**
 * Get user by ID or UUID
 * 
 * @urlParam user integer|string required User ID (integer) or UUID (string). Example: 1 or "550e8400-e29b-41d4-a716-446655440000"
 * 
 * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "user_name": "john.doe",
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com"
     *   }
     * }
     * @response 404 {
     *   "message": "User not found"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
     * @authenticated
     */
public function show(User $user): JsonResponse
{
    // Implementation
}
```

#### 3.1.3. Store Method (Create Resource)

```php
/**
 * Create a new user
 * 
 * @bodyParam user_name string required Username. Example: john.doe
 * @bodyParam first_name string required First name. Example: John
 * @bodyParam last_name string required Last name. Example: Doe
 * @bodyParam email string required Email address. Example: john.doe@example.com
 * @bodyParam phone string nullable Phone number. Example: +84123456789
 * @bodyParam role string required User role. Example: student
 * @bodyParam code string nullable User code. Example: ST001
 * @bodyParam department_id integer nullable Department ID. Example: 1
 * @bodyParam faculty_id integer nullable Faculty ID. Example: 1
 * @bodyParam password string required Password (min 8 characters). Example: password123
 * 
 * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_name": "john.doe",
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com"
     *   }
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
     * @authenticated
     */
public function store(StoreUserRequest $request): JsonResponse
{
    // Implementation
}
```

#### 3.1.4. Update Method

```php
/**
 * Update user
 * 
 * @urlParam user integer|string required User ID or UUID. Example: 1
 * @bodyParam first_name string First name. Example: John
 * @bodyParam last_name string Last name. Example: Doe
 * @bodyParam email string Email address. Example: john.doe@example.com
 * 
 * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_name": "john.doe",
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com"
     *   }
     * }
     * @response 404 {
     *   "message": "User not found"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     * 
     * @authenticated
     */
public function update(UpdateUserRequest $request, User $user): JsonResponse
{
    // Implementation
}
```

#### 3.1.5. Delete Method

```php
/**
 * Delete user
 * 
 * @urlParam user integer|string required User ID or UUID. Example: 1
 * 
 * @response 204
     * @response 404 {
     *   "message": "User not found"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
     * @authenticated
     */
public function destroy(User $user): JsonResponse
{
    // Implementation
}
```

#### 3.1.6. Custom Actions

```php
/**
 * Reset user password to default
 * 
 * @urlParam user integer|string required User ID or UUID. Example: 1
 * 
 * @response 200 {
     *   "success": true,
     *   "message": "Mật khẩu đã được reset thành công!"
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Forbidden"
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "Có lỗi xảy ra khi reset mật khẩu!"
     * }
     * 
     * @authenticated
     */
public function resetPassword(User $user): JsonResponse
{
    // Implementation
}
```

### 3.2. Request Validation Documentation

Scramble tự động detect validation rules từ FormRequest classes. Tuy nhiên, có thể bổ sung thêm documentation:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam user_name string required Username (unique). Example: john.doe
 * @bodyParam first_name string required First name. Example: John
 * @bodyParam last_name string required Last name. Example: Doe
 * @bodyParam email string required Email address (unique). Example: john.doe@example.com
 * @bodyParam phone string nullable Phone number. Example: +84123456789
 * @bodyParam role string required User role. Example: student
 * @bodyParam code string nullable User code (unique). Example: ST001
 * @bodyParam department_id integer nullable Department ID. Example: 1
 * @bodyParam faculty_id integer nullable Faculty ID. Example: 1
 * @bodyParam password string required Password (min 8 characters). Example: password123
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Implementation
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'code' => 'nullable|max:255|unique:users,code',
            'department_id' => 'nullable',
            'faculty_id' => 'nullable',
            'password' => 'required|min:8|max:255',
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
            'user_name.required' => 'Username is required',
            'user_name.unique' => 'Username already exists',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
        ];
    }
}
```

### 3.3. API Resource Documentation

Scramble tự động detect structure từ API Resources. Có thể bổ sung documentation:

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $uuid
 * @property string $user_name
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $role
 * @property string|null $code
 * @property int|null $department_id
 * @property int|null $faculty_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_name' => $this->user_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'code' => $this->code,
            'department_id' => $this->department_id,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->whenLoaded('faculty', fn () => new FacultyResource($this->faculty)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
```

### 3.4. Authentication Documentation

#### 3.4.1. OAuth2 (Laravel Passport)

```php
/**
 * Authenticate user and get access token
 * 
 * @bodyParam grant_type string required Grant type. Example: password
 * @bodyParam client_id string required OAuth client ID. Example: 1
 * @bodyParam client_secret string required OAuth client secret. Example: xxxxxxxxxxxxxx
 * @bodyParam username string required Username or email. Example: john.doe@example.com
 * @bodyParam password string required Password. Example: password123
 * @bodyParam scope string Scope. Example: *
 * 
 * @response 200 {
     *   "token_type": "Bearer",
     *   "expires_in": 31536000,
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
     * }
     * @response 401 {
     *   "error": "invalid_client",
     *   "error_description": "Client authentication failed"
     * }
     * 
 * @unauthenticated
 */
public function authenticate(Request $request): JsonResponse
{
    // Implementation
}
```

#### 3.4.2. API Token Authentication

```php
/**
 * Get authenticated user
 * 
 * @response 200 {
     *   "id": 1,
     *   "user_name": "john.doe",
     *   "email": "john.doe@example.com"
     * }
     * @response 401 {
     *   "message": "Unauthenticated"
     * }
     * 
 * @authenticated
 */
public function user(Request $request): JsonResponse
{
    return response()->json($request->user());
}
```

### 3.5. Error Responses Documentation

Luôn document các error responses có thể xảy ra:

```php
/**
 * Get user by ID
 * 
 * @urlParam user integer|string required User ID or UUID. Example: 1
 * 
 * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_name": "john.doe"
     *   }
     * }
     * @response 400 {
     *   "message": "Bad Request",
     *   "errors": {
     *     "user": ["Invalid user identifier"]
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "User not found"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     * 
 * @authenticated
 */
public function show(User $user): JsonResponse
{
    // Implementation
}
```

## 4. Best Practices

### 4.1. Documentation Structure

1. **Group Related Endpoints**: Sử dụng `@group` để nhóm các endpoints liên quan
2. **Consistent Naming**: Sử dụng naming nhất quán cho parameters
3. **Complete Examples**: Luôn cung cấp examples cho mọi parameter
4. **Error Documentation**: Document tất cả error responses có thể xảy ra

### 4.2. Parameter Documentation

- **Query Parameters**: Sử dụng `@queryParam`
- **URL Parameters**: Sử dụng `@urlParam`
- **Body Parameters**: Sử dụng `@bodyParam`
- **Headers**: Sử dụng `@headerParam` (nếu cần)

### 4.3. Response Documentation

- **Success Responses**: Document với `@response` và status code
- **Error Responses**: Document tất cả error cases
- **Response Structure**: Sử dụng JSON examples để mô tả structure

### 4.4. Authentication Documentation

- **Public Endpoints**: Sử dụng `@unauthenticated`
- **Protected Endpoints**: Sử dụng `@authenticated`
- **OAuth2**: Document grant types và scopes

### 4.5. UUID Support Documentation

Khi support cả integer ID và UUID:

```php
/**
 * Get user by ID or UUID
 * 
 * @urlParam user integer|string required User ID (integer) or UUID (string). 
 *   Example: 1 or "550e8400-e29b-41d4-a716-446655440000"
 * 
 * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "user_name": "john.doe"
     *   }
     * }
 */
public function show(User $user): JsonResponse
{
    // Implementation
}
```

## 5. Examples cho Dự án Hiện tại

### 5.1. UserController - Index Method

```php
/**
 * Get list of users
 * 
 * @group Users
 * 
 * @queryParam page integer Page number. Example: 1
 * @queryParam per_page integer Items per page. Example: 15
 * @queryParam search string Search term (searches in user_name, first_name, last_name, email). Example: john
 * @queryParam faculty_ids array List of faculty IDs to filter. Example: [1, 2]
 * @queryParam roles array List of roles to filter. Example: ["student", "teacher"]
 * 
 * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *       "user_name": "john.doe",
     *       "first_name": "John",
     *       "last_name": "Doe",
     *       "email": "john.doe@example.com",
     *       "role": "student",
     *       "faculty": {
     *         "id": 1,
     *         "name": "Faculty of Engineering"
     *       }
     *     }
     *   ],
     *   "current_page": 1,
     *   "per_page": 15,
     *   "total": 100,
     *   "last_page": 7
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
 * @authenticated
 */
public function index(Request $request): JsonResponse
{
    // Implementation
}
```

### 5.2. UserController - Store Method

```php
/**
 * Create a new user
 * 
 * @group Users
 * 
 * @bodyParam user_name string required Username (unique, max 255 characters). Example: john.doe
 * @bodyParam first_name string required First name (max 255 characters). Example: John
 * @bodyParam last_name string required Last name (max 255 characters). Example: Doe
 * @bodyParam email string required Email address (unique, valid email format). Example: john.doe@example.com
 * @bodyParam phone string nullable Phone number (max 255 characters). Example: +84123456789
 * @bodyParam role string required User role. Example: student
 * @bodyParam code string nullable User code (unique, max 255 characters). Example: ST001
 * @bodyParam department_id integer nullable Department ID. Example: 1
 * @bodyParam faculty_id integer nullable Faculty ID. Example: 1
 * @bodyParam password string required Password (min 8 characters, max 255 characters). Example: password123
 * 
 * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "user_name": "john.doe",
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "role": "student"
     *   }
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "user_name": ["The user name has already been taken."]
     *   }
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
 * @authenticated
 */
public function store(StoreUserRequest $request): JsonResponse
{
    // Implementation
}
```

### 5.3. UserController - Reset Password Method

```php
/**
 * Reset user password to default 'password'
 * 
 * @group Users
 * 
 * @urlParam user integer|string required User ID (integer) or UUID (string). Example: 1 or "550e8400-e29b-41d4-a716-446655440000"
 * 
 * @response 200 {
     *   "success": true,
     *   "message": "Mật khẩu đã được reset thành công!"
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found"
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "Có lỗi xảy ra khi reset mật khẩu!"
     * }
     * 
 * @authenticated
 */
public function resetPassword(User $user): JsonResponse
{
    // Implementation
}
```

## 6. Checklist cho API Documentation

Khi viết API documentation, đảm bảo:

- [ ] ✅ Method có PHPDoc với description rõ ràng
- [ ] ✅ Tất cả parameters được document (query, url, body)
- [ ] ✅ Mỗi parameter có example
- [ ] ✅ Success response được document với status code và structure
- [ ] ✅ Tất cả error responses được document
- [ ] ✅ Authentication requirement được document (`@authenticated` hoặc `@unauthenticated`)
- [ ] ✅ Group được chỉ định (`@group`)
- [ ] ✅ Examples là realistic và meaningful
- [ ] ✅ Response structure match với API Resource structure

## 7. Tài liệu Tham khảo

- **Scramble Documentation**: https://scramble.dedoc.co/
- **Laravel API Resources**: https://laravel.com/docs/eloquent-resources
- **Laravel Form Requests**: https://laravel.com/docs/validation#form-request-validation

---

**Last Updated**: 2024-12-14  
**Maintained By**: Development Team
