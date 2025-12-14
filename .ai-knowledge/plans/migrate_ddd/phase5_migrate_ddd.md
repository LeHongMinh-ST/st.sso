# Phase 5: API Endpoints Migration - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## 🚨 SECURITY FIRST - ĐỌC TRƯỚC KHI BẮT ĐẦU

**⚠️ CRITICAL WARNING**: Phase 5 liên quan đến **API endpoints** - các điểm truy cập công khai của hệ thống. Cần tuân thủ nghiêm ngặt các security best practices.

### 🔒 Security là Priority #1

**Mọi quyết định trong Phase 5 phải được đánh giá qua lăng kính bảo mật:**

1. ✅ **API Security**: Token validation, rate limiting, input validation
2. ✅ **Authorization**: Proper permission checks
3. ✅ **Data Protection**: No sensitive data exposure
4. ✅ **Error Handling**: Generic error messages

### 📋 Mandatory Security Requirements

**TRƯỚC KHI BẮT ĐẦU BẤT KỲ TASK NÀO:**

- [ ] ✅ Phase 1, 2, 3, và 4 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã đọc và hiểu **Security Considerations** từ Phase 3 và Phase 4
- [ ] ✅ Đã hiểu rõ API endpoints hiện tại
- [ ] ✅ Đã hiểu rõ API authentication mechanism (tokens)
- [ ] ✅ Đã hiểu rõ API authorization (permissions)
- [ ] ✅ Đã có security expert available để review code

**LUÔN LUÔN:**
- ✅ Validate tokens properly
- ✅ Check permissions at API level
- ✅ Validate all inputs
- ✅ Use generic error messages
- ✅ Rate limit API endpoints
- ✅ Log API security events

---

## Tổng quan

Phase 5 tập trung vào việc migrate **API endpoints** sang kiến trúc DDD. Đây là phase tích hợp các Use Cases từ Phase 2 (OrganizationalStructure) và Phase 3 (IdentityAccess) vào API layer, đảm bảo API endpoints hoạt động đúng cách với DDD architecture.

### Mục tiêu Phase 5

1. ✅ Migrate User API endpoints sang sử dụng Use Cases từ OrganizationalStructure Context
2. ✅ Migrate Faculty API endpoints sang sử dụng Use Cases từ OrganizationalStructure Context
3. ✅ Migrate SSO Authentication API sang sử dụng Use Cases từ IdentityAccess Context
4. ✅ Tạo API Resources mới để transform Domain objects thành API responses
5. ✅ Đảm bảo backward compatibility với existing API clients
6. ✅ Đảm bảo security best practices được áp dụng
7. ✅ Đảm bảo code quality cao với test coverage >= 90%

### Thời gian ước tính

**Tổng thời gian**: 6-9 ngày làm việc (48-72 giờ)

**Phân bổ**:
- Task 5.1: Migrate User API (2-3 ngày) - includes Contract Tests
- Task 5.2: Migrate Faculty API (1-2 ngày)
- Task 5.3: API Versioning Strategy (1 ngày) - if needed
- Task 5.4: Migrate SSO Authentication API (2-3 ngày)

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 5, đảm bảo:

### Setup Checklist

- [ ] ✅ Phase 1, 2, 3, và 4 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã review và approve architecture design trong `.ai-knowledge/commons/02-architecture.md`
- [ ] ✅ Đã setup development environment (PHP 8.3, Laravel 12, Composer)
- [ ] ✅ Đã tạo feature branch: `feat/migrate-ddd-phase5`
- [ ] ✅ Đã hiểu rõ API endpoints hiện tại (`routes/api.php`)
- [ ] ✅ Đã review existing API Controllers (`Api\UserController`, `Api\FacultyController`, `Api\AuthenticateSSOController`)
- [ ] ✅ Đã hiểu rõ API Resources hiện tại
- [ ] ✅ Đã hiểu rõ API authentication mechanism (Laravel Passport tokens)
- [ ] ✅ Đã có database backup
- [ ] ✅ Đã setup CI/CD để chạy tests tự động
- [ ] ✅ Đã có API documentation (nếu có)

### Environment Verification

**Steps để verify environment**:
1. [ ] Check Phase 4 completion:
   ```bash
   # Verify IdentityAccess Infrastructure exists
   ls -la app/IdentityAccess/Infrastructure/Http/Controllers/Auth/
   ls -la app/IdentityAccess/Infrastructure/Http/Middleware/
   # Should see: AuthenticateController, ValidateTokenMiddleware
   ```
2. [ ] Check Phase 2 và Phase 3 completion:
   ```bash
   # Verify OrganizationalStructure Use Cases exist
   ls -la app/OrganizationalStructure/Application/UseCases/
   # Should see: CreateUserUseCase, FindUserUseCase, etc.
   ```
3. [ ] Check existing API routes:
   ```bash
   cat routes/api.php
   php artisan route:list --path=api
   ```
4. [ ] Check existing API Controllers:
   ```bash
   ls -la app/Http/Controllers/Api/
   ```
5. [ ] Check PHP version:
   ```bash
   php -v  # Should be PHP 8.3.x
   ```
6. [ ] Check Git branch:
   ```bash
   git branch  # Should be on feat/migrate-ddd-phase5
   ```
7. [ ] Run existing tests:
   ```bash
   php artisan test
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ Phase 2, 3, 4 components available
- [ ] ✅ Existing API code reviewed
- [ ] ✅ Environment ready for development

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (15-20 phút)
   - [ ] Đọc task description và requirements
   - [ ] Review existing API endpoint để hiểu business logic
   - [ ] Review Use Cases từ Phase 2 và Phase 3
   - [ ] Xác định security implications
   - [ ] Xác định dependencies và prerequisites
   - [ ] Estimate time

2. **Security Review** (10-15 phút) - **CRITICAL**
   - [ ] Identify security risks
   - [ ] Plan security measures (token validation, rate limiting, input validation)
   - [ ] Review authorization requirements
   - [ ] Plan error handling (không leak sensitive info)

3. **Setup** (15-20 phút)
   - [ ] Tạo files và folders cần thiết
   - [ ] Verify prerequisites
   - [ ] Setup test files structure

4. **Implementation** (varies)
   - [ ] Implement theo TDD (Test-Driven Development)
   - [ ] Write tests first (Red)
   - [ ] Implement code (Green)
   - [ ] Refactor (Refactor)
   - [ ] Follow DDD principles và conventions
   - [ ] **Apply security best practices**

5. **Security Testing** (varies) - **CRITICAL**
   - [ ] Unit tests
   - [ ] Feature tests
   - [ ] Integration tests
   - [ ] **API security tests**: Token validation, authorization bypass, input validation

6. **Code Quality** (15-20 phút)
   - [ ] Run Laravel Pint
   - [ ] Run PHPStan (nếu có)
   - [ ] Check test coverage (>= 90%)
   - [ ] Review code với security lens

7. **Documentation** (10-15 phút)
   - [ ] Update PHPDoc comments
   - [ ] Add inline comments cho complex logic
   - [ ] Document security considerations
   - [ ] Update API documentation (nếu có)

8. **Commit** (10-15 phút)
   - [ ] Stage files
   - [ ] Write meaningful commit message
   - [ ] **Never commit secrets/passwords**
   - [ ] Push to feature branch

9. **Review** (varies)
   - [ ] Self-review code với security focus
   - [ ] Request security review từ team
   - [ ] Address feedback

### Best Practices cho API Development

- ✅ **RESTful Design**: Follow REST conventions
- ✅ **Input Validation**: Validate ALL inputs rigorously
- ✅ **Output Transformation**: Use API Resources để transform data
- ✅ **Error Handling**: Consistent error responses
- ✅ **Rate Limiting**: Implement cho API endpoints
- ✅ **Token Security**: Proper token validation
- ✅ **Authorization**: Check permissions at API level
- ✅ **Versioning**: Plan API versioning (if needed)

---

## Task 5.1: Migrate User API

**Estimated Time**: 2-3 ngày (16-24 giờ)

**Mục tiêu**: Migrate User API endpoints sang sử dụng Use Cases từ OrganizationalStructure Context

### Task 5.1.1: Review Existing User API

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing `Api\UserController`:
   ```bash
   cat app/Http/Controllers/Api/UserController.php
   ```
2. [ ] Identify endpoints cần migrate:
   - [ ] `index()` - List users với pagination và filters
   - [ ] `show()` - Get single user
   - [ ] `store()` - Create new user
   - [ ] `resetPassword()` - Reset user password
3. [ ] Review existing `UserResource`:
   ```bash
   cat app/Http/Resources/User/UserResource.php
   ```
4. [ ] Review API routes:
   ```bash
   grep -A 5 "users" routes/api.php
   ```
5. [ ] Document current API behavior:
   - [ ] Request/Response formats
   - [ ] Authentication requirements
   - [ ] Authorization requirements
   - [ ] Query parameters và filters
   - [ ] Error responses

**Verification**:
- [ ] Existing code reviewed
- [ ] Endpoints identified
- [ ] Behavior documented

---

### Task 5.1.2: Create New User API Controller với UUID Support

**Estimated Time**: 4 giờ

**⚠️ UUID Migration Impact**: Controller cần support cả integer ID và UUID để backward compatible với external systems.

**Steps**:
1. [ ] Review UUID Migration Strategy:
   - [ ] Đọc `.ai-knowledge/migration-strategy/uuid-migration-strategy.md`
   - [ ] Đọc `.ai-knowledge/migration-strategy/uuid-migration-external-systems.md`
   - [ ] Review IdMappingService implementation
   - [ ] Hiểu rõ API compatibility requirements
2. [ ] Tạo folder structure:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Http/Controllers/Api
   touch app/OrganizationalStructure/Infrastructure/Http/Controllers/Api/UserController.php
   ```
3. [ ] Implement controller với UUID support:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Infrastructure\Http\Controllers\Api;
   
   use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
   use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
   use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
   use App\OrganizationalStructure\Application\UseCases\FindUsersByFacultyUseCase;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\OrganizationalStructure\Infrastructure\Http\Resources\UserResource;
   use App\SharedKernel\Infrastructure\Services\IdMappingService;
   use Illuminate\Http\JsonResponse;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   
   /**
    * User API controller.
    * Handles User API endpoints using OrganizationalStructure Use Cases.
    */
   final class UserController
   {
       public function __construct(
           private readonly CreateUserUseCase $createUserUseCase,
           private readonly FindUserUseCase $findUserUseCase,
           private readonly FindUsersByFacultyUseCase $findUsersByFacultyUseCase
       ) {
       }
       
       /**
        * List users với pagination và filters.
        */
       public function index(Request $request): JsonResponse
       {
           // Check permission
           if (!Auth::guard('api')->user()->can('viewAny', \App\Models\User::class)) {
               return response()->json([
                   'error' => 'Forbidden',
                   'message' => 'Insufficient permissions'
               ], 403);
           }
           
           // Get filters từ request
           $facultyId = $request->input('faculty_id');
           $search = $request->input('search');
           $role = $request->input('role');
           $perPage = $request->input('per_page', 15);
           
           // Use FindUsersByFacultyUseCase hoặc create new Use Case for listing
           // For now, we'll use existing Use Case
           if ($facultyId) {
               $users = $this->findUsersByFacultyUseCase->execute((int) $facultyId);
           } else {
               // Need to create FindUsersUseCase for general listing
               // Temporary: Use existing method
               $users = [];
           }
           
           return UserResource::collection($users)->response();
       }
       
       /**
        * Get single user - supports both integer ID and UUID.
        * 
        * GET /api/users/{identifier}
        * - identifier can be: 123 (integer) or {uuid}
        */
       public function show(Request $request, string $identifier): JsonResponse
       {
           // Resolve identifier (supports both formats)
           $mapping = $this->idMappingService->resolveIdentifier('users', $identifier);

           if (!$mapping['uuid']) {
               return response()->json([
                   'error' => 'Not Found',
                   'message' => 'User not found'
               ], 404);
           }

           // Check permission (use integer ID for policy check)
           if ($mapping['id'] && !Auth::guard('api')->user()->can('view', \App\Models\User::find($mapping['id']))) {
               return response()->json([
                   'error' => 'Forbidden',
                   'message' => 'Insufficient permissions'
               ], 403);
           }
           
           try {
               // Use UUID to find user in Domain layer
               $userId = UserId::fromString($mapping['uuid']);
               $user = $this->findUserUseCase->execute($userId);
               
               if ($user === null) {
                   return response()->json([
                       'error' => 'Not Found',
                       'message' => 'User not found'
                   ], 404);
               }
               
               // Return response with both IDs for backward compatibility
               return response()->json([
                   'id' => $mapping['id'],        // Integer ID (legacy)
                   'uuid' => $mapping['uuid'],    // UUID (new)
                   'data' => (new UserResource($user))->toArray($request),
               ]);
           } catch (\InvalidArgumentException $e) {
               return response()->json([
                   'error' => 'Bad Request',
                   'message' => 'Invalid user identifier format'
               ], 400);
           }
       }
       
       /**
        * Create new user.
        */
       public function store(Request $request): JsonResponse
       {
           // Check permission
           if (!Auth::guard('api')->user()->can('create', \App\Models\User::class)) {
               return response()->json([
                   'error' => 'Forbidden',
                   'message' => 'Insufficient permissions'
               ], 403);
           }
           
           // Validate input
           $validated = $request->validate([
               'first_name' => 'required|string|max:255',
               'last_name' => 'required|string|max:255',
               'email' => 'required|email|max:255',
               'code' => 'nullable|string|max:50',
               'phone' => 'nullable|string|max:20',
               'faculty_id' => 'nullable|integer|exists:faculties,id',
               'department_id' => 'nullable|integer|exists:departments,id',
           ]);
           
           try {
               $dto = new CreateUserDTO(
                   firstName: $validated['first_name'],
                   lastName: $validated['last_name'],
                   email: $validated['email'],
                   studentCode: $validated['code'] ?? null,
                   phone: $validated['phone'] ?? null,
                   facultyId: $validated['faculty_id'] ?? null,
                   departmentId: $validated['department_id'] ?? null
               );
               
               $user = $this->createUserUseCase->execute($dto);
               
               return (new UserResource($user))->response()->setStatusCode(201);
           } catch (\Exception $e) {
               Log::error('User creation error', [
                   'error' => $e->getMessage(),
                   'trace' => $e->getTraceAsString(),
               ]);
               
               return response()->json([
                   'error' => 'Internal Server Error',
                   'message' => 'Failed to create user'
               ], 500);
           }
       }
       
       /**
        * Reset user password.
        */
       public function resetPassword(string $id): JsonResponse
       {
           // Check permission
           $currentUser = Auth::guard('api')->user();
           $targetUser = \App\Models\User::find($id);
           
           if (!$currentUser->can('resetPassword', $targetUser)) {
               return response()->json([
                   'error' => 'Forbidden',
                   'message' => 'Insufficient permissions'
               ], 403);
           }
           
           // Use ChangePasswordUseCase từ IdentityAccess Context
           // Implementation depends on Use Case availability
           
           return response()->json([
               'success' => true,
               'message' => 'Password reset successfully'
           ]);
       }
   }
   ```
3. [ ] Verify implementation:
   - [ ] Controller là `final`
   - [ ] Dependencies injected via constructor
   - [ ] Uses Use Cases
   - [ ] Proper error handling
   - [ ] Generic error messages
   - [ ] Permission checks

**Code Review Checklist**:
- [ ] ✅ Controller là `final`
- [ ] ✅ Dependencies injected via constructor
- [ ] ✅ Uses Use Cases từ OrganizationalStructure
- [ ] ✅ Proper error handling
- [ ] ✅ Generic error messages
- [ ] ✅ Input validation
- [ ] ✅ Permission checks
- [ ] ✅ Proper HTTP status codes

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Token validation** (handled by middleware)
- [ ] ✅ **CRITICAL: Permission checks** at controller level
- [ ] ✅ **CRITICAL: Input validation** comprehensive
- [ ] ✅ **CRITICAL: Generic error messages** (không leak info)
- [ ] ✅ **CRITICAL: No sensitive data in responses**
- [ ] ✅ Proper exception handling
- [ ] ✅ Rate limiting (handled by middleware)

**Verification**:
- [ ] Syntax check passes
- [ ] IDE không có errors
- [ ] Security review passed

---

### Task 5.1.3: Create User API Resource

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo API Resource mới để transform User Domain object thành API response

**Steps**:
1. [ ] Tạo API Resource:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Http/Resources
   touch app/OrganizationalStructure/Infrastructure/Http/Resources/UserResource.php
   ```
2. [ ] Implement Resource:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Infrastructure\Http\Resources;
   
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use Illuminate\Http\Request;
   use Illuminate\Http\Resources\Json\JsonResource;
   
   /**
    * User API resource.
    * Transforms User Domain object to API response.
    */
   final class UserResource extends JsonResource
   {
       /**
        * Transform the resource into an array.
        *
        * @return array<string, mixed>
        */
       public function toArray(Request $request): array
       {
           /** @var User $user */
           $user = $this->resource;
           
           return [
               'id' => $user->id()->toString(),
               'email' => $user->email()->toString(),
               'first_name' => $user->fullName()->firstName(),
               'last_name' => $user->fullName()->lastName(),
               'full_name' => $user->fullName()->fullName(),
               'code' => $user->studentCode()?->toString(),
               'phone' => $user->phoneNumber()->isEmpty() ? null : $user->phoneNumber()->toString(),
               'faculty_id' => $user->facultyId()?->toInt(),
               'department_id' => $user->departmentId()?->toInt(),
               'created_at' => $user->createdAt()?->toIso8601String(),
               'updated_at' => $user->updatedAt()?->toIso8601String(),
           ];
       }
   }
   ```
3. [ ] Verify implementation:
   - [ ] Transforms Domain object correctly
   - [ ] No sensitive data exposed
   - [ ] Proper data types
   - [ ] Handles nullable fields

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: No sensitive data** (passwords, tokens)
- [ ] ✅ **CRITICAL: No internal IDs** exposed (if applicable)
- [ ] ✅ Proper data transformation
- [ ] ✅ Handles null values correctly

**Verification**:
- [ ] Resource created
- [ ] Transforms correctly
- [ ] Security review passed

---

### Task 5.1.4: Create FindUsersUseCase (if needed)

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo Use Case để list users với filters và pagination (nếu chưa có)

**Steps**:
1. [ ] Check if Use Case exists trong Phase 2
2. [ ] If not, create `FindUsersUseCase`:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Application\UseCases;
   
   use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
   
   /**
    * Use case for finding users with filters và pagination.
    */
   final class FindUsersUseCase
   {
       public function __construct(
           private readonly UserRepositoryInterface $userRepository
       ) {
       }
       
       /**
        * Find users với filters.
        *
        * @return array{users: User[], total: int}
        */
       public function execute(
           ?int $facultyId = null,
           ?int $departmentId = null,
           ?string $search = null,
           ?string $role = null,
           int $page = 1,
           int $perPage = 15
       ): array {
           // Use repository methods to find users
           if ($facultyId !== null) {
               $users = $this->userRepository->findByFacultyId($facultyId);
           } elseif ($departmentId !== null) {
               $users = $this->userRepository->findByDepartmentId($departmentId);
           } else {
               // Need to add findAll() method to repository
               $users = $this->userRepository->findAll($page, $perPage);
           }
           
           // Apply filters
           if ($search !== null) {
               $users = $this->filterBySearch($users, $search);
           }
           
           if ($role !== null) {
               $users = $this->filterByRole($users, $role);
           }
           
           return [
               'users' => $users,
               'total' => count($users),
           ];
       }
       
       private function filterBySearch(array $users, string $search): array
       {
           // Implement search filtering
           return array_filter($users, function ($user) use ($search) {
               return stripos($user->fullName()->fullName(), $search) !== false
                   || stripos($user->email()->toString(), $search) !== false;
           });
       }
       
       private function filterByRole(array $users, string $role): array
       {
           // Note: Role filtering might need to check UserIdentity
           // This is a simplified version
           return $users;
       }
   }
   ```
3. [ ] Add `findAll()` method to UserRepositoryInterface nếu cần
4. [ ] Write unit tests

**Verification**:
- [ ] Use Case created (if needed)
- [ ] Repository methods added (if needed)
- [ ] Tests pass

---

### Task 5.1.5: Update API Routes

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Update `routes/api.php`:
   ```php
   use App\OrganizationalStructure\Infrastructure\Http\Controllers\Api\UserController;
   
   Route::middleware(['auth:api'])->group(function (): void {
       // User API
       Route::apiResource('users', UserController::class)
           ->only(['index', 'show', 'store']);
       Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword']);
   });
   ```
2. [ ] Verify routes:
   ```bash
   php artisan route:list --path=api/users
   ```
3. [ ] Test routes manually:
   - [ ] GET `/api/users` - should list users
   - [ ] GET `/api/users/{id}` - should get single user
   - [ ] POST `/api/users` - should create user
   - [ ] POST `/api/users/{id}/reset-password` - should reset password

**Verification**:
- [ ] Routes updated
- [ ] Routes work correctly
- [ ] Manual testing successful

---

### Task 5.1.6: Write API Tests

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Tạo test file:
   ```bash
   mkdir -p tests/Feature/OrganizationalStructure/Api
   touch tests/Feature/OrganizationalStructure/Api/UserApiTest.php
   ```
2. [ ] Implement tests:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace Tests\Feature\OrganizationalStructure\Api;
   
   use App\Models\User;
   use Illuminate\Foundation\Testing\RefreshDatabase;
   use Laravel\Passport\Passport;
   use Tests\TestCase;
   
   class UserApiTest extends TestCase
   {
       use RefreshDatabase;
       
       public function test_index_returns_users_with_pagination(): void
       {
           $user = User::factory()->create();
           Passport::actingAs($user);
           
           $response = $this->getJson('/api/users');
           
           $response->assertStatus(200);
           $response->assertJsonStructure([
               'data' => [
                   '*' => ['id', 'email', 'first_name', 'last_name']
               ],
               'links',
               'meta'
           ]);
       }
       
       public function test_index_requires_authentication(): void
       {
           $response = $this->getJson('/api/users');
           
           $response->assertStatus(401);
       }
       
       public function test_index_requires_permission(): void
       {
           $user = User::factory()->create();
           // User without permission
           Passport::actingAs($user);
           
           $response = $this->getJson('/api/users');
           
           $response->assertStatus(403);
       }
       
       public function test_show_returns_single_user(): void
       {
           $user = User::factory()->create();
           Passport::actingAs($user);
           
           $targetUser = User::factory()->create();
           
           $response = $this->getJson("/api/users/{$targetUser->id}");
           
           $response->assertStatus(200);
           $response->assertJsonStructure([
               'data' => ['id', 'email', 'first_name', 'last_name']
           ]);
       }
       
       public function test_store_creates_new_user(): void
       {
           $user = User::factory()->create();
           Passport::actingAs($user);
           
           $response = $this->postJson('/api/users', [
               'first_name' => 'John',
               'last_name' => 'Doe',
               'email' => 'john@example.com',
           ]);
           
           $response->assertStatus(201);
           $response->assertJsonStructure([
               'data' => ['id', 'email', 'first_name', 'last_name']
           ]);
           
           $this->assertDatabaseHas('users', [
               'email' => 'john@example.com',
           ]);
       }
       
       public function test_store_validates_input(): void
       {
           $user = User::factory()->create();
           Passport::actingAs($user);
           
           $response = $this->postJson('/api/users', []);
           
           $response->assertStatus(422);
           $response->assertJsonValidationErrors(['first_name', 'last_name', 'email']);
       }
   }
   ```
3. [ ] Run tests:
   ```bash
   php artisan test tests/Feature/OrganizationalStructure/Api/UserApiTest.php
   ```

**Security Tests Required**:
- [ ] ✅ Test authentication required
- [ ] ✅ Test permission checks
- [ ] ✅ Test input validation
- [ ] ✅ Test generic error messages
- [ ] ✅ Test no sensitive data in responses

**Verification**:
- [ ] All API tests pass
- [ ] Security tests pass
- [ ] Test coverage >= 90%

---

### Task 5.1.7: Commit User API Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all tests
3. [ ] Check coverage (>= 90%)
4. [ ] Security review
5. [ ] Manual API testing:
   - [ ] Test với Postman hoặc similar tool
   - [ ] Test authentication
   - [ ] Test authorization
   - [ ] Test error handling
6. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Infrastructure/Http/Controllers/Api/
   git add app/OrganizationalStructure/Infrastructure/Http/Resources/
   git add routes/api.php
   git add tests/Feature/OrganizationalStructure/Api/
   git commit -m "feat(OrganizationalStructure): migrate User API to DDD

   - Refactor UserController to use Use Cases
   - Create UserResource for API responses
   - Add FindUsersUseCase for listing users
   - Update API routes
   - Add comprehensive API tests with 90%+ coverage
   - Security: Token validation, permission checks, input validation"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Security review passed
- [ ] Manual API testing successful
- [ ] Commit successful

---

## Task 5.2: Migrate Faculty API

**Estimated Time**: 1-2 ngày (8-16 giờ)

**Mục tiêu**: Migrate Faculty API endpoints sang sử dụng Use Cases từ OrganizationalStructure Context

### Task 5.2.1: Review Existing Faculty API

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing `Api\FacultyController`
2. [ ] Identify endpoints:
   - [ ] `index()` - List faculties với pagination
   - [ ] `all()` - Get all faculties
   - [ ] `getUsers()` - Get users by faculty
   - [ ] `getTeachers()` - Get teachers by faculty
   - [ ] `getDepartments()` - Get departments by faculty
3. [ ] Review existing `FacultyResource`
4. [ ] Document current behavior

**Verification**:
- [ ] Existing code reviewed
- [ ] Endpoints identified
- [ ] Behavior documented

---

### Task 5.2.2: Create New Faculty API Controller

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Tạo controller:
   ```bash
   touch app/OrganizationalStructure/Infrastructure/Http/Controllers/Api/FacultyController.php
   ```
2. [ ] Implement controller sử dụng Use Cases từ Phase 2
3. [ ] Write tests

**Verification**:
- [ ] Controller created
- [ ] Tests pass

---

### Task 5.2.3: Create Faculty API Resource

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Tạo `FacultyResource`
2. [ ] Implement transformation
3. [ ] Write tests

**Verification**:
- [ ] Resource created
- [ ] Tests pass

---

### Task 5.2.4: Update API Routes và Commit

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Update routes
2. [ ] Run tests
3. [ ] Manual testing
4. [ ] Commit

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

## Task 5.3: API Versioning Strategy (if needed)

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Implement API versioning nếu có breaking changes hoặc external clients

### Task 5.3.1: Assess Need for API Versioning

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Review API changes:
   - [ ] Response format changes?
   - [ ] Request format changes?
   - [ ] Endpoint changes?
   - [ ] Breaking changes?
2. [ ] Check external API clients:
   - [ ] Có external clients không?
   - [ ] Clients có thể update không?
   - [ ] Timeline để migrate clients?
3. [ ] Decision:
   - [ ] **Option A**: No versioning (backward compatible changes only)
   - [ ] **Option B**: API versioning (v1, v2)
   - [ ] **Option C**: Feature flags (gradual migration)

**Verification**:
- [ ] Need assessed
- [ ] Decision made
- [ ] Documented

---

### Task 5.3.2: Implement API Versioning (if needed)

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Setup API versioning:
   ```php
   // routes/api.php
   Route::prefix('v1')->group(function () {
       // Old API endpoints
       Route::apiResource('users', OldUserController::class);
   });
   
   Route::prefix('v2')->group(function () {
       // New DDD API endpoints
       Route::apiResource('users', UserController::class);
   });
   ```
2. [ ] Add version detection middleware:
   ```php
   // Detect version from header hoặc URL
   Route::middleware(['api.version'])->group(function () {
       // Routes
   });
   ```
3. [ ] Add deprecation headers cho v1:
   ```php
   // In v1 controllers
   return response()->json($data)
       ->header('X-API-Deprecated', 'true')
       ->header('X-API-Deprecation-Date', '2024-12-31')
       ->header('X-API-Sunset-Date', '2025-03-31');
   ```
4. [ ] Create migration guide cho API clients
5. [ ] Write tests

**Verification**:
- [ ] API versioning implemented (if needed)
- [ ] Tests pass
- [ ] Migration guide created

---

### Task 5.3.3: Document API Versioning Strategy

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Document versioning approach
2. [ ] Document migration path cho clients
3. [ ] Document deprecation timeline
4. [ ] Update API documentation

**Verification**:
- [ ] Strategy documented
- [ ] Migration path documented
- [ ] API docs updated

---

## Task 5.4: Migrate SSO Authentication API

**Estimated Time**: 2-3 ngày (16-24 giờ)

**Mục tiêu**: Migrate SSO Authentication API sang sử dụng Use Cases từ IdentityAccess Context

### Task 5.4.1: Review Existing SSO API

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review `Api\AuthenticateSSOController` (hiện tại empty)
2. [ ] Review OAuth2 flow requirements
3. [ ] Review Laravel Passport OAuth2 endpoints
4. [ ] Document requirements

**Verification**:
- [ ] Requirements reviewed
- [ ] Flow documented

---

### Task 5.4.2: Implement SSO Authentication Endpoints

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Implement OAuth2 token endpoint sử dụng `IssueAccessTokenUseCase`
2. [ ] Implement token validation endpoint
3. [ ] Implement token revocation endpoint
4. [ ] Write tests

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Token security**
- [ ] ✅ **CRITICAL: Client validation**
- [ ] ✅ **CRITICAL: Scope validation**
- [ ] ✅ **CRITICAL: Rate limiting**

**Verification**:
- [ ] Endpoints implemented
- [ ] Tests pass
- [ ] Security review passed

---

### Task 5.4.3: Update OAuth Flow và Commit

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Update OAuth2 routes
2. [ ] Test OAuth2 flow end-to-end
3. [ ] Commit

**Verification**:
- [ ] OAuth2 flow works
- [ ] Tests pass
- [ ] Commit successful

---

## Milestones và Progress Tracking

### Milestone 5.1: User API Migrated

**Estimated Time**: 2-3 ngày

**Progress Checklist**:
- [ ] ✅ UserController refactored và tested
- [ ] ✅ UserResource created và tested
- [ ] ✅ API routes updated
- [ ] ✅ API tests coverage >= 90%
- [ ] ✅ Security review passed

**Status**: ⏳ In Progress

---

### Milestone 5.2: Faculty API Migrated

**Estimated Time**: 1-2 ngày

**Progress Checklist**:
- [ ] ✅ FacultyController refactored và tested
- [ ] ✅ FacultyResource created và tested
- [ ] ✅ API routes updated
- [ ] ✅ API tests coverage >= 90%
- [ ] ✅ Security review passed

**Status**: ⏳ Pending

---

### Milestone 5.3: API Versioning Complete (if needed)

**Estimated Time**: 1 ngày

**Progress Checklist**:
- [ ] ✅ API versioning need assessed
- [ ] ✅ API versioning implemented (if needed)
- [ ] ✅ Migration guide created
- [ ] ✅ Documentation updated

**Status**: ⏳ Pending

---

### Milestone 5.4: SSO Authentication API Migrated

**Estimated Time**: 2-3 ngày

**Progress Checklist**:
- [ ] ✅ SSO endpoints implemented và tested
- [ ] ✅ OAuth2 flow updated
- [ ] ✅ API tests coverage >= 90%
- [ ] ✅ Security review passed

**Status**: ⏳ Pending

---

## Quick Reference Commands

### Development Commands

```bash
# Run tests
php artisan test

# Run specific API test
php artisan test tests/Feature/OrganizationalStructure/Api/UserApiTest.php

# Run with coverage
php artisan test --coverage --min=90

# List API routes
php artisan route:list --path=api

# Code formatting
./vendor/bin/pint app/OrganizationalStructure/Infrastructure/Http/Controllers/Api/
```

### API Testing Commands

```bash
# Test API endpoints với curl
curl -X GET http://localhost/api/users \
  -H "Authorization: Bearer {token}"

# Test với Postman collection (if available)
```

### Git Commands

```bash
# Create feature branch
git checkout -b feat/migrate-ddd-phase5

# Stage files
git add app/OrganizationalStructure/Infrastructure/Http/Controllers/Api/

# Commit
git commit -m "feat(OrganizationalStructure): migrate User API to DDD"

# Push
git push origin feat/migrate-ddd-phase5
```

---

## Troubleshooting Guide

### Issue: API returns 401 Unauthorized

**Symptoms**: API requests fail với 401

**Root Cause**: Token validation issue hoặc middleware không applied

**Solution**:
1. Check token format
2. Verify ValidateTokenMiddleware applied
3. Check token expiration
4. Verify token signature
5. Check middleware registration

**Prevention**: Write comprehensive token validation tests

---

### Issue: API returns 403 Forbidden

**Symptoms**: API requests fail với 403

**Root Cause**: Permission check fails

**Solution**:
1. Check AuthorizationService implementation
2. Verify permission codes match
3. Check Policy registration
4. Verify user has required permissions
5. Check permission checks in controller

**Prevention**: Write comprehensive authorization tests

---

### Issue: API response format không đúng

**Symptoms**: API response không match expected format

**Root Cause**: API Resource transformation issue

**Solution**:
1. Check API Resource implementation
2. Verify data transformation
3. Check nullable fields handling
4. Verify data types

**Prevention**: Write comprehensive API Resource tests

---

### Issue: Backward compatibility broken

**Symptoms**: Existing API clients break

**Root Cause**: API response format changed hoặc endpoints changed

**Solution**:
1. Review API changes
2. Check response format compatibility
3. Update API documentation
4. Consider API versioning
5. Communicate changes to clients

**Prevention**: Maintain backward compatibility, use API versioning if needed

---

## Security Best Practices Checklist

### API Security

- [ ] ✅ Token validation proper
- [ ] ✅ Permission checks at API level
- [ ] ✅ Input validation comprehensive
- [ ] ✅ Generic error messages
- [ ] ✅ Rate limiting implemented
- [ ] ✅ CORS configured properly
- [ ] ✅ HTTPS only in production

### Data Protection

- [ ] ✅ No sensitive data in responses
- [ ] ✅ No internal IDs exposed (if applicable)
- [ ] ✅ Proper data transformation
- [ ] ✅ PII protection

### Error Handling

- [ ] ✅ Consistent error format
- [ ] ✅ Generic error messages
- [ ] ✅ Proper HTTP status codes
- [ ] ✅ No stack traces in production

### General

- [ ] ✅ Input validation everywhere
- [ ] ✅ Output encoding
- [ ] ✅ Logging without sensitive data
- [ ] ✅ Security headers configured

---

## Common Questions & Answers

### Q: Có cần API versioning không?

**A**: Tùy vào requirements:
- **Nếu có external clients**: Nên có API versioning
- **Nếu chỉ internal use**: Có thể không cần ngay
- **Best practice**: Plan API versioning từ đầu

**Recommendation**: Implement API versioning nếu có external clients hoặc plan for future.

---

### Q: Làm sao đảm bảo backward compatibility?

**A**: 
1. Maintain existing response format
2. Keep existing endpoints
3. Add new endpoints với versioning
4. Communicate changes to clients
5. Provide migration guide

**Recommendation**: Maintain backward compatibility trong transition period.

---

### Q: Test coverage bao nhiêu là đủ cho API?

**A**: 
- **API endpoints**: >= 90% (critical)
- **API Resources**: >= 85%
- **Error handling**: >= 90%

**Recommendation**: Focus vào quality và critical paths.

---

### Q: Có cần rate limiting cho tất cả endpoints?

**A**: 
- **Authentication endpoints**: Yes (critical)
- **Write endpoints**: Yes (recommended)
- **Read endpoints**: Optional (depends on requirements)

**Recommendation**: Implement rate limiting cho authentication và write endpoints minimum.

---

## Summary

Phase 5 là phase migrate API endpoints sang DDD architecture, đảm bảo API hoạt động đúng cách với Use Cases từ Phase 2 và Phase 3. Cần:

1. ✅ **Follow workflow**: Planning → Security Review → Implementation → Testing → Review
2. ✅ **Small commits**: Commit sau mỗi API endpoint migration
3. ✅ **TDD**: Write tests trước khi implement
4. ✅ **Security first**: Always consider security implications
5. ✅ **Backward compatibility**: Đảm bảo không break existing API clients
6. ✅ **Testing**: Unit tests, Feature tests, Integration tests, API tests, Security tests
7. ✅ **Documentation**: Update API documentation

**Estimated Total Time**: 5-8 ngày làm việc (40-64 giờ)

**Success Criteria**:
- ✅ User API migrated và tested
- ✅ Faculty API migrated và tested
- ✅ SSO Authentication API migrated và tested
- ✅ Test coverage >= 90%
- ✅ Security review passed
- ✅ Backward compatibility maintained
- ✅ Code quality standards met
- ✅ API documentation updated

**⚠️ REMEMBER**: API endpoints are public-facing. Always prioritize security và test thoroughly before production.

---

## Deployment Strategy

**⚠️ CRITICAL**: Phase 5 liên quan đến API endpoints - public-facing. Cần deployment strategy cẩn thận để đảm bảo backward compatibility.

### Pre-Deployment Checklist

**Trước khi deploy Phase 5, đảm bảo**:

- [ ] ✅ **All tests pass** (100% pass rate, >= 90% coverage)
- [ ] ✅ **API tests pass** (all endpoints tested)
- [ ] ✅ **Backward compatibility verified** (existing API clients still work)
- [ ] ✅ **API documentation updated**
- [ ] ✅ **Code review completed**
- [ ] ✅ **Database backup created**
- [ ] ✅ **Staging environment tested** với real API clients
- [ ] ✅ **Feature flags configured** (nếu cần)
- [ ] ✅ **API versioning strategy** (nếu có breaking changes)
- [ ] ✅ **Rollback plan ready**
- [ ] ✅ **Monitoring setup** với API metrics
- [ ] ✅ **API clients notified** (nếu có breaking changes)

---

### Deployment Steps

#### Step 1: Pre-Deployment (30 phút)

1. [ ] **Create deployment branch**
2. [ ] **Final API testing**:
   ```bash
   php artisan test --filter=ApiTest
   php artisan test tests/Feature/OrganizationalStructure/Api/
   ```
3. [ ] **API documentation check**:
   - [ ] Endpoints documented
   - [ ] Request/response examples
   - [ ] Error responses documented
4. [ ] **Backward compatibility check**:
   - [ ] Test với existing API clients
   - [ ] Verify response format compatible
   - [ ] Verify no breaking changes

---

#### Step 2: Staging Deployment (1 giờ)

1. [ ] **Deploy to staging**
2. [ ] **Staging API testing**:
   - [ ] Test all API endpoints
   - [ ] Test authentication
   - [ ] Test authorization
   - [ ] Test error handling
   - [ ] Test với Postman/API client
   - [ ] Performance testing
3. [ ] **Fix any issues**

---

#### Step 3: Production Deployment (1 giờ)

**Strategy**: Feature flags hoặc API versioning

**Option A: Feature Flags**

1. [ ] **Enable feature flags** (OFF initially):
   ```php
   'new_user_api' => env('FEATURE_NEW_USER_API', false),
   'new_faculty_api' => env('FEATURE_NEW_FACULTY_API', false),
   'new_sso_api' => env('FEATURE_NEW_SSO_API', false),
   ```

2. [ ] **Deploy code** (features OFF)

3. [ ] **Verify** old API endpoints work

4. [ ] **Gradual rollout**:
   - Week 1: Enable cho internal API clients
   - Week 2: Enable cho 25% API clients
   - Week 3: Enable cho 50% API clients
   - Week 4: Enable cho 100% API clients

**Option B: API Versioning**

1. [ ] **Deploy new API** với version:
   ```
   /api/v2/users
   /api/v2/faculties
   ```

2. [ ] **Keep old API** active:
   ```
   /api/v1/users  (old)
   /api/v1/faculties  (old)
   ```

3. [ ] **Migrate API clients** gradually:
   - Week 1: Internal clients migrate
   - Week 2-4: External clients migrate
   - Week 5+: Deprecate v1 (with notice)

4. [ ] **Monitor** both versions

---

#### Step 4: Post-Deployment Verification (30 phút)

1. [ ] **API smoke tests**:
   ```bash
   # Test với curl hoặc Postman
   curl -X GET https://api.example.com/api/users \
     -H "Authorization: Bearer {token}"
   ```

2. [ ] **Check API logs**:
   - [ ] Request logs
   - [ ] Error logs
   - [ ] Performance logs

3. [ ] **Check monitoring**:
   - [ ] API response time
   - [ ] API error rate
   - [ ] API request rate
   - [ ] Token validation success rate

4. [ ] **Check API clients**:
   - [ ] Verify existing clients still work
   - [ ] Monitor client errors
   - [ ] Collect client feedback

---

### Rollback Procedures

**Nếu có API issues**:

1. [ ] **Disable feature flags** immediately:
   ```bash
   FEATURE_NEW_USER_API=false
   php artisan config:cache
   ```

2. [ ] **Verify** old API endpoints work

3. [ ] **Investigate** issues:
   - [ ] Check API logs
   - [ ] Check error responses
   - [ ] Check client reports

4. [ ] **Fix** và redeploy

**Full rollback** (nếu critical):
1. [ ] Revert code
2. [ ] Verify old API works
3. [ ] Notify API clients
4. [ ] Document issues

---

### API Versioning Strategy (if needed)

**If breaking changes**:

1. [ ] **Create new API version**:
   ```
   /api/v2/users
   /api/v2/faculties
   ```

2. [ ] **Keep old version** active:
   ```
   /api/v1/users  (old, deprecated)
   ```

3. [ ] **Add deprecation notice**:
   ```json
   {
     "deprecated": true,
     "deprecation_date": "2024-12-31",
     "sunset_date": "2025-03-31",
     "migration_guide": "https://docs.example.com/api/migration-v1-to-v2"
   }
   ```

4. [ ] **Migrate clients** gradually

5. [ ] **Deprecate v1** after migration period

---

### Feature Flags Strategy

**Implementation**:

```php
// config/features.php
'new_user_api' => env('FEATURE_NEW_USER_API', false),
'new_faculty_api' => env('FEATURE_NEW_FACULTY_API', false),
'new_sso_api' => env('FEATURE_NEW_SSO_API', false),
```

**Usage trong routes**:
```php
if (Feature::active('new_user_api')) {
    Route::apiResource('users', NewUserController::class);
} else {
    Route::apiResource('users', OldUserController::class);
}
```

**Rollout**:
- Week 1: Internal API clients
- Week 2: 25% API clients
- Week 3: 50% API clients
- Week 4: 100% API clients

---

### Monitoring Checklist

**After deployment**:
- [ ] ✅ **API response time** (should be < 200ms)
- [ ] ✅ **API error rate** (should be < 0.1%)
- [ ] ✅ **API request rate** (should be normal)
- [ ] ✅ **Token validation success rate** (should be > 99%)
- [ ] ✅ **Authorization success rate** (should be normal)
- [ ] ✅ **Client errors** (should be 0 or very low)
- [ ] ✅ **API documentation** accuracy

**API Monitoring Tools**:
- API Gateway logs
- Application logs
- APM tools
- API analytics
- Client error tracking

---

### Deployment Timeline

**Recommended Timeline**:
- Day 1: Staging deployment và API testing
- Day 2: Production deployment với feature flags OFF
- Day 3-4: Enable cho internal API clients
- Day 5-7: Gradually enable cho external API clients
- Day 8+: Full rollout

**⚠️ Note**: API deployment nên conservative hơn vì có external clients.
