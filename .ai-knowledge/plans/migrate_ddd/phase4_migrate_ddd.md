# Phase 4: Authentication & Authorization Flow - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## 🚨 SECURITY FIRST - ĐỌC TRƯỚC KHI BẮT ĐẦU

**⚠️ CRITICAL WARNING**: Phase 4 liên quan đến **authentication flow** và **authorization middleware** - các thành phần bảo mật quan trọng. Cần tuân thủ nghiêm ngặt các security best practices từ Phase 3.

### 🔒 Security là Priority #1

**Mọi quyết định trong Phase 4 phải được đánh giá qua lăng kính bảo mật:**

1. ✅ **Security trước, Performance sau**: Chọn giải pháp an toàn hơn
2. ✅ **Fail securely**: Default deny, explicit allow
3. ✅ **Defense in depth**: Multiple layers of security
4. ✅ **When in doubt, choose security**: Khi không chắc chắn, chọn giải pháp an toàn hơn

### 📋 Mandatory Security Requirements

**TRƯỚC KHI BẮT ĐẦU BẤT KỲ TASK NÀO:**

- [ ] ✅ Phase 3 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã đọc và hiểu **Security Considerations** từ Phase 3
- [ ] ✅ Đã đọc và hiểu **Security Patterns và Anti-Patterns** từ Phase 3
- [ ] ✅ Đã hiểu rõ authentication flow hiện tại
- [ ] ✅ Đã hiểu rõ authorization mechanism hiện tại (Policies, Middleware)
- [ ] ✅ Đã có security expert available để review code

**LUÔN LUÔN:**
- ✅ Validate tokens properly
- ✅ Check permissions at multiple layers
- ✅ Use generic error messages
- ✅ Log security events
- ✅ Implement rate limiting
- ✅ Follow security patterns từ Phase 3

---

## Tổng quan

Phase 4 tập trung vào việc migrate **authentication flow** và **authorization middleware** sang kiến trúc DDD. Đây là phase tích hợp các Use Cases từ Phase 3 (IdentityAccess) vào infrastructure layer, đảm bảo authentication và authorization hoạt động đúng cách trong toàn bộ application.

### Mục tiêu Phase 4

1. ✅ Migrate Authentication Flow sang sử dụng Use Cases từ IdentityAccess Context
2. ✅ Migrate Authorization (Policies & Middleware) sang sử dụng Use Cases từ IdentityAccess Context
3. ✅ Tạo Authorization Service trong Application layer để centralize permission checks
4. ✅ Đảm bảo backward compatibility với existing routes và controllers
5. ✅ Đảm bảo security best practices được áp dụng
6. ✅ Đảm bảo code quality cao với test coverage >= 90%

### Thời gian ước tính

**Tổng thời gian**: 7-9 ngày làm việc (56-72 giờ)

**Phân bổ**:
- Task 4.1: Migrate Authentication Flow (3-4 ngày)
- Task 4.2: Migrate Authorization (3-4 ngày) - includes Cross-Context Integration Testing

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 4, đảm bảo:

### Setup Checklist

- [ ] ✅ Phase 1, Phase 2, và Phase 3 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã review và approve architecture design trong `.ai-knowledge/commons/02-architecture.md`
- [ ] ✅ Đã setup development environment (PHP 8.3, Laravel 12, Composer)
- [ ] ✅ Đã tạo feature branch: `feat/migrate-ddd-phase4`
- [ ] ✅ Đã hiểu rõ authentication flow hiện tại (`AuthenticateController`, Microsoft auth)
- [ ] ✅ Đã hiểu rõ authorization mechanism hiện tại (Policies, Middleware)
- [ ] ✅ Đã review existing routes (`routes/web.php`, `routes/api.php`)
- [ ] ✅ Đã có database backup
- [ ] ✅ Đã setup CI/CD để chạy tests tự động

### Environment Verification

**Steps để verify environment**:
1. [ ] Check Phase 3 completion:
   ```bash
   # Verify IdentityAccess Context exists
   ls -la app/IdentityAccess/Domain/Aggregates/
   ls -la app/IdentityAccess/Application/UseCases/
   # Should see: UserIdentity, AuthenticateUserUseCase, CheckPermissionUseCase, etc.
   ```
2. [ ] Check existing authentication code:
   ```bash
   # Review AuthenticateController
   cat app/Http/Controllers/Auth/AuthenticateController.php
   ```
3. [ ] Check existing policies:
   ```bash
   ls -la app/Policies/
   # Should see: UserPolicy, FacultyPolicy, etc.
   ```
4. [ ] Check routes:
   ```bash
   cat routes/web.php
   cat routes/api.php
   ```
5. [ ] Check PHP version:
   ```bash
   php -v  # Should be PHP 8.3.x
   ```
6. [ ] Check Git branch:
   ```bash
   git branch  # Should be on feat/migrate-ddd-phase4
   ```
7. [ ] Run existing tests:
   ```bash
   php artisan test
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ Phase 3 components available
- [ ] ✅ Existing code reviewed
- [ ] ✅ Environment ready for development

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (15-20 phút)
   - [ ] Đọc task description và requirements
   - [ ] Review existing code để hiểu business logic
   - [ ] Review Use Cases từ Phase 3
   - [ ] Xác định security implications
   - [ ] Xác định dependencies và prerequisites
   - [ ] Estimate time

2. **Security Review** (10-15 phút) - **CRITICAL**
   - [ ] Identify security risks
   - [ ] Plan security measures
   - [ ] Review authentication/authorization flow
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
   - [ ] **Security tests**: Authentication bypass, authorization bypass, token manipulation

6. **Code Quality** (15-20 phút)
   - [ ] Run Laravel Pint
   - [ ] Run PHPStan (nếu có)
   - [ ] Check test coverage (>= 90%)
   - [ ] Review code với security lens

7. **Documentation** (10-15 phút)
   - [ ] Update PHPDoc comments
   - [ ] Add inline comments cho complex logic
   - [ ] Document security considerations
   - [ ] Update routes documentation

8. **Commit** (10-15 phút)
   - [ ] Stage files
   - [ ] Write meaningful commit message
   - [ ] **Never commit secrets/passwords**
   - [ ] Push to feature branch

9. **Review** (varies)
   - [ ] Self-review code với security focus
   - [ ] Request security review từ team
   - [ ] Address feedback

### Best Practices cho Security-Critical Code

- ✅ **Token Validation**: Always validate tokens properly
- ✅ **Permission Checks**: Check permissions at multiple layers
- ✅ **Error Messages**: Generic error messages, không leak info
- ✅ **Audit Logging**: Log authentication và authorization events
- ✅ **Rate Limiting**: Implement cho authentication endpoints
- ✅ **Fail Securely**: Default deny, explicit allow

---

## Task 4.1: Migrate Authentication Flow

**Estimated Time**: 3-4 ngày (24-32 giờ)

**Mục tiêu**: Migrate authentication flow sang sử dụng Use Cases từ IdentityAccess Context

### Task 4.1.1: Refactor AuthenticateController

**Estimated Time**: 1.5 ngày (12 giờ)

**Mục tiêu**: Refactor `AuthenticateController` để sử dụng `AuthenticateUserUseCase`

#### Subtask 4.1.1.1: Review Existing AuthenticateController

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing `AuthenticateController`:
   ```bash
   cat app/Http/Controllers/Auth/AuthenticateController.php
   ```
2. [ ] Identify methods cần migrate:
   - [ ] `showLoginForm()` - có thể giữ nguyên (chỉ render view)
   - [ ] `login()` - cần migrate sang Use Case
   - [ ] `logout()` - có thể giữ nguyên hoặc migrate
   - [ ] `redirectToSocialite()` - có thể giữ nguyên (chỉ redirect)
   - [ ] `handleSocialteCallback()` - cần migrate sang Use Case
3. [ ] Document current flow:
   - [ ] Login flow với username/password
   - [ ] Microsoft authentication flow
   - [ ] Session management
   - [ ] Error handling

**Verification**:
- [ ] Existing code reviewed
- [ ] Methods identified
- [ ] Flow documented

---

#### Subtask 4.1.1.2: Create New AuthenticateController trong Infrastructure

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo folder structure:
   ```bash
   mkdir -p app/IdentityAccess/Infrastructure/Http/Controllers/Auth
   touch app/IdentityAccess/Infrastructure/Http/Controllers/Auth/AuthenticateController.php
   ```
2. [ ] Implement controller sử dụng Use Cases:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Http\Controllers\Auth;
   
   use App\IdentityAccess\Application\DTOs\AuthenticateUserDTO;
   use App\IdentityAccess\Application\UseCases\AuthenticateUserUseCase;
   use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
   use Illuminate\Http\RedirectResponse;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\View\View;
   
   /**
    * Authentication controller.
    * Handles user authentication using IdentityAccess Use Cases.
    */
   final class AuthenticateController
   {
       public function __construct(
           private readonly AuthenticateUserUseCase $authenticateUserUseCase
       ) {
       }
       
       /**
        * Show login form.
        */
       public function showLoginForm(): View|RedirectResponse
       {
           if (Auth::check()) {
               return redirect()->route('dashboard');
           }
           
           return view('pages.auth.login');
       }
       
       /**
        * Handle login request.
        */
       public function login(Request $request): RedirectResponse
       {
           $request->validate([
               'username' => 'required|string',
               'password' => 'required|string',
               'remember' => 'nullable|boolean',
           ]);
           
           try {
               $dto = new AuthenticateUserDTO(
                   username: $request->input('username'),
                   password: $request->input('password')
               );
               
               $userIdentity = $this->authenticateUserUseCase->execute($dto);
               
               // Get User model từ OrganizationalStructure để login
               // Note: Cần bridge giữa UserIdentity và User model
               $user = $this->getUserModelFromIdentity($userIdentity);
               
               Auth::login($user, (bool) $request->input('remember', false));
               
               return redirect()->intended(route('dashboard'));
           } catch (InvalidCredentialsException $e) {
               return redirect()->back()
                   ->withErrors(['message' => ['Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!']])
                   ->withInput($request->only('username'));
           }
       }
       
       /**
        * Handle logout request.
        */
       public function logout(): RedirectResponse
       {
           Auth::logout();
           
           return redirect()->route('login');
       }
       
       /**
        * Get User model from UserIdentity.
        * This is a bridge method between IdentityAccess và OrganizationalStructure contexts.
        */
       private function getUserModelFromIdentity($userIdentity)
       {
           // Implementation: Query User model using organizationalStructureUserId
           // This is a temporary bridge until full migration
           return \App\Models\User::find($userIdentity->organizationalStructureUserId());
       }
   }
   ```
3. [ ] Verify implementation:
   - [ ] Controller là `final`
   - [ ] Dependencies injected via constructor
   - [ ] Uses Use Cases
   - [ ] Proper error handling
   - [ ] Generic error messages

**Code Review Checklist**:
- [ ] ✅ Controller là `final`
- [ ] ✅ Dependencies injected via constructor
- [ ] ✅ Uses `AuthenticateUserUseCase`
- [ ] ✅ Proper error handling
- [ ] ✅ Generic error messages (không leak info)
- [ ] ✅ Input validation
- [ ] ✅ Session management secure

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Generic error messages** (không reveal if user exists)
- [ ] ✅ **CRITICAL: Input validation** (username, password)
- [ ] ✅ **CRITICAL: Rate limiting** (handled by Use Case)
- [ ] ✅ **CRITICAL: Secure session management** (HttpOnly, Secure, SameSite)
- [ ] ✅ **CRITICAL: No sensitive data in logs**
- [ ] ✅ Proper exception handling
- [ ] ✅ CSRF protection (Laravel tự động)

**Verification**:
- [ ] Syntax check passes
- [ ] IDE không có errors
- [ ] Security review passed

---

#### Subtask 4.1.1.3: Create Bridge Service (Temporary)

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo bridge service để link UserIdentity với User model (temporary cho đến khi migration hoàn tất)

**Steps**:
1. [ ] Tạo bridge service:
   ```bash
   mkdir -p app/IdentityAccess/Infrastructure/Services
   touch app/IdentityAccess/Infrastructure/Services/UserIdentityBridgeService.php
   ```
2. [ ] Implement bridge service:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Services;
   
   use App\IdentityAccess\Domain\Aggregates\UserIdentity;
   use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
   use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
   use App\Models\User as EloquentUser;
   
   /**
    * Bridge service to link UserIdentity với User model.
    * TEMPORARY: This will be removed after full migration.
    * 
    * Strategy:
    * - During migration: Bridge between DDD aggregates và Eloquent models
    * - After migration: Remove bridge, use DDD aggregates directly
    * - Removal target: Phase 6
    */
   final class UserIdentityBridgeService
   {
       public function __construct(
           private readonly UserIdentityRepositoryInterface $userIdentityRepository
       ) {
       }
       
       /**
        * Get Eloquent User model from UserIdentity.
        * Used for Laravel Auth system compatibility during migration.
        */
       public function getEloquentUser(UserIdentity $userIdentity): ?EloquentUser
       {
           $userId = $userIdentity->organizationalStructureUserId();
           return EloquentUser::find($userId);
       }
       
       /**
        * Get UserIdentity from Eloquent User model.
        * Used to convert existing Eloquent User to UserIdentity.
        */
       public function getUserIdentity(EloquentUser $user): ?UserIdentity
       {
           $userIdentityId = UserIdentityId::fromOrganizationalStructureUserId((string) $user->id);
           return $this->userIdentityRepository->findById($userIdentityId);
       }
       
       /**
        * Get UserIdentityId from Eloquent User.
        */
       public function getUserIdentityId(EloquentUser $user): UserIdentityId
       {
           return UserIdentityId::fromOrganizationalStructureUserId((string) $user->id);
       }
   }
   ```
3. [ ] Register bridge service trong Service Provider
4. [ ] Update AuthenticateController để sử dụng bridge service
5. [ ] Write tests cho bridge service

**Bridge Service Strategy**:
- **Purpose**: Temporary compatibility layer giữa DDD aggregates và Eloquent models
- **Usage**: Chỉ sử dụng trong Infrastructure layer, không expose ra Domain/Application
- **Removal**: Plan remove trong Phase 6 sau khi full migration hoàn tất
- **Documentation**: Document rõ ràng là temporary và removal plan

**Verification**:
- [ ] Bridge service created
- [ ] Registered trong Service Provider
- [ ] Controller updated
- [ ] Tests pass
- [ ] Documented as temporary

---

#### Subtask 4.1.1.4: Update Routes

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Update `routes/web.php`:
   ```php
   use App\IdentityAccess\Infrastructure\Http\Controllers\Auth\AuthenticateController;
   
   Route::get('/login', [AuthenticateController::class, 'showLoginForm'])->name('login');
   Route::post('/login', [AuthenticateController::class, 'login'])->name('handleLogin');
   Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');
   ```
2. [ ] Verify routes work:
   ```bash
   php artisan route:list | grep login
   ```
3. [ ] Test routes manually:
   - [ ] Visit `/login` - should show login form
   - [ ] Submit login form - should authenticate
   - [ ] Visit `/logout` - should logout

**Verification**:
- [ ] Routes updated
- [ ] Routes work correctly
- [ ] Manual testing successful

---

#### Subtask 4.1.1.5: Write Feature Tests

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo test file:
   ```bash
   mkdir -p tests/Feature/IdentityAccess/Http/Controllers/Auth
   touch tests/Feature/IdentityAccess/Http/Controllers/Auth/AuthenticateControllerTest.php
   ```
2. [ ] Implement tests:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace Tests\Feature\IdentityAccess\Http\Controllers\Auth;
   
   use App\IdentityAccess\Domain\Aggregates\UserIdentity;
   use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
   use App\IdentityAccess\Domain\ValueObjects\Username;
   use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\Models\User;
   use Illuminate\Foundation\Testing\RefreshDatabase;
   use Illuminate\Support\Facades\Hash;
   use Tests\TestCase;
   
   class AuthenticateControllerTest extends TestCase
   {
       use RefreshDatabase;
       
       public function test_show_login_form_returns_view(): void
       {
           $response = $this->get('/login');
           
           $response->assertStatus(200);
           $response->assertViewIs('pages.auth.login');
       }
       
       public function test_show_login_form_redirects_if_authenticated(): void
       {
           $user = User::factory()->create();
           
           $response = $this->actingAs($user)->get('/login');
           
           $response->assertRedirect(route('dashboard'));
       }
       
       public function test_login_success_authenticates_user(): void
       {
           // Create user identity và user model
           $user = User::factory()->create([
               'email' => 'test@example.com',
               'password' => Hash::make('password123'),
           ]);
           
           // Create user identity (this would be done via Use Case in real scenario)
           // For test, we'll mock the Use Case
           
           $response = $this->post('/login', [
               'username' => 'test@example.com',
               'password' => 'password123',
           ]);
           
           $this->assertAuthenticatedAs($user);
           $response->assertRedirect(route('dashboard'));
       }
       
       public function test_login_fails_with_invalid_credentials(): void
       {
           $response = $this->post('/login', [
               'username' => 'test@example.com',
               'password' => 'wrongpassword',
           ]);
           
           $this->assertGuest();
           $response->assertRedirect();
           $response->assertSessionHasErrors('message');
       }
       
       public function test_logout_logs_out_user(): void
       {
           $user = User::factory()->create();
           
           $response = $this->actingAs($user)->post('/logout');
           
           $this->assertGuest();
           $response->assertRedirect(route('login'));
       }
   }
   ```
3. [ ] Run tests:
   ```bash
   php artisan test tests/Feature/IdentityAccess/Http/Controllers/Auth/AuthenticateControllerTest.php
   ```

**Security Tests Required**:
- [ ] ✅ Test rate limiting works (after multiple failed attempts)
- [ ] ✅ Test generic error messages (no information leakage)
- [ ] ✅ Test CSRF protection
- [ ] ✅ Test session security

**Verification**:
- [ ] All feature tests pass
- [ ] Security tests pass
- [ ] Test coverage >= 90%

---

#### Subtask 4.1.1.6: Commit AuthenticateController Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all tests
3. [ ] Check coverage (>= 90%)
4. [ ] Security review
5. [ ] Manual testing:
   - [ ] Test login với valid credentials
   - [ ] Test login với invalid credentials
   - [ ] Test logout
   - [ ] Test rate limiting
6. [ ] Commit:
   ```bash
   git add app/IdentityAccess/Infrastructure/Http/Controllers/Auth/
   git add app/IdentityAccess/Infrastructure/Services/UserIdentityBridgeService.php
   git add routes/web.php
   git add tests/Feature/IdentityAccess/Http/Controllers/Auth/
   git commit -m "feat(IdentityAccess): migrate AuthenticateController to DDD

   - Refactor AuthenticateController to use AuthenticateUserUseCase
   - Add UserIdentityBridgeService for temporary bridge
   - Update routes to use new controller
   - Add comprehensive feature tests with 90%+ coverage
   - Security: Generic error messages, rate limiting, secure sessions"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Security review passed
- [ ] Manual testing successful
- [ ] Commit successful

---

### Task 4.1.2: Refactor Microsoft Authentication

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Refactor Microsoft authentication để sử dụng `AuthenticateWithMicrosoftUseCase`

#### Subtask 4.1.2.1: Review Existing Microsoft Auth Flow

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review `redirectToSocialite()` method
2. [ ] Review `handleSocialteCallback()` method
3. [ ] Understand Laravel Socialite Azure flow
4. [ ] Document current flow

**Verification**:
- [ ] Existing code reviewed
- [ ] Flow documented

---

#### Subtask 4.1.2.2: Implement AuthenticateWithMicrosoftUseCase Integration

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Update `handleSocialteCallback()` để sử dụng Use Case:
   ```php
   public function handleSocialteCallback(
       AuthenticateWithMicrosoftUseCase $authenticateWithMicrosoftUseCase
   ): RedirectResponse {
       try {
           $azureUser = Socialite::driver('azure')->stateless()->user();
           
           $dto = new AuthenticateWithMicrosoftDTO(
               email: $azureUser->getEmail(),
               name: $azureUser->getName(),
               azureId: $azureUser->getId()
           );
           
           $userIdentity = $authenticateWithMicrosoftUseCase->execute($dto);
           
           // Get User model và login
           $user = $this->userIdentityBridgeService->getEloquentUser($userIdentity);
           
           if ($user === null) {
               return redirect()->route('login')
                   ->withErrors(['message' => ['Tài khoản chưa tồn tại trong hệ thống']]);
           }
           
           Auth::login($user, true);
           
           return redirect()->intended(route('dashboard', absolute: false));
       } catch (\Exception $e) {
           Log::error('Microsoft authentication error', [
               'error' => $e->getMessage(),
           ]);
           
           return redirect()->route('login')
               ->withErrors(['message' => ['Đăng nhập thất bại. Vui lòng thử lại.']]);
       }
   }
   ```
2. [ ] Verify implementation
3. [ ] Write feature tests

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Generic error messages**
- [ ] ✅ **CRITICAL: Proper exception handling**
- [ ] ✅ **CRITICAL: No sensitive data in logs**
- [ ] ✅ Input validation
- [ ] ✅ Secure session management

**Verification**:
- [ ] Implementation complete
- [ ] Tests pass
- [ ] Security review passed

---

#### Subtask 4.1.2.3: Commit Microsoft Authentication Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Manual testing với Microsoft Azure
4. [ ] Commit

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

### Task 4.1.3: Create Token Validation Middleware

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tạo middleware mới sử dụng `ValidateTokenUseCase` cho API authentication

#### Subtask 4.1.3.1: Review Existing API Authentication

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing API routes (`routes/api.php`)
2. [ ] Review existing `auth:api` middleware
3. [ ] Understand Laravel Passport token validation
4. [ ] Document current flow

**Verification**:
- [ ] Existing code reviewed
- [ ] Flow documented

---

#### Subtask 4.1.3.2: Create ValidateTokenMiddleware

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Tạo middleware:
   ```bash
   mkdir -p app/IdentityAccess/Infrastructure/Http/Middleware
   touch app/IdentityAccess/Infrastructure/Http/Middleware/ValidateTokenMiddleware.php
   ```
2. [ ] Implement middleware:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Http\Middleware;
   
   use App\IdentityAccess\Application\UseCases\ValidateTokenUseCase;
   use App\IdentityAccess\Domain\Exceptions\InvalidTokenException;
   use Closure;
   use Illuminate\Http\Request;
   use Symfony\Component\HttpFoundation\Response;
   
   /**
    * Middleware to validate access tokens.
    */
   final class ValidateTokenMiddleware
   {
       public function __construct(
           private readonly ValidateTokenUseCase $validateTokenUseCase
       ) {
       }
       
       /**
        * Handle an incoming request.
        */
       public function handle(Request $request, Closure $next): Response
       {
           $token = $request->bearerToken();
           
           if ($token === null) {
               return response()->json([
                   'error' => 'Unauthorized',
                   'message' => 'Token not provided'
               ], 401);
           }
           
           try {
               $userInfo = $this->validateTokenUseCase->execute($token);
               
               if ($userInfo === null) {
                   return response()->json([
                       'error' => 'Unauthorized',
                       'message' => 'Invalid token'
                   ], 401);
               }
               
               // Attach user info to request
               $request->merge(['user_info' => $userInfo]);
               
               return $next($request);
           } catch (InvalidTokenException $e) {
               return response()->json([
                   'error' => 'Unauthorized',
                   'message' => 'Invalid token'
               ], 401);
           }
       }
   }
   ```
3. [ ] Register middleware trong `bootstrap/app.php` hoặc `app/Http/Kernel.php`
4. [ ] Write tests

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Token validation proper**
- [ ] ✅ **CRITICAL: Generic error messages**
- [ ] ✅ **CRITICAL: No sensitive data in responses**
- [ ] ✅ Proper exception handling
- [ ] ✅ Token extraction secure

**Verification**:
- [ ] Middleware created
- [ ] Registered correctly
- [ ] Tests pass
- [ ] Security review passed

---

#### Subtask 4.1.3.3: Update API Routes

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Update `routes/api.php` để sử dụng middleware mới:
   ```php
   Route::middleware(['validate.token'])->group(function (): void {
       Route::get('/user', fn (Request $request) => $request->user());
       // Other API routes
   });
   ```
2. [ ] Test API endpoints
3. [ ] Verify backward compatibility

**Verification**:
- [ ] Routes updated
- [ ] API endpoints work
- [ ] Backward compatibility maintained

---

#### Subtask 4.1.3.4: Commit Token Validation Middleware

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Manual API testing
4. [ ] Commit

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

### Task 4.1.4: Commit Authentication Flow Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run all tests
2. [ ] Check coverage (>= 90%)
3. [ ] Security review
4. [ ] Integration testing:
   - [ ] Test full login flow
   - [ ] Test Microsoft authentication flow
   - [ ] Test API token validation
   - [ ] Test rate limiting
   - [ ] Test error handling
5. [ ] Final commit:
   ```bash
   git add app/IdentityAccess/Infrastructure/Http/
   git add routes/
   git add tests/
   git commit -m "feat(IdentityAccess): complete Authentication Flow migration

   - Migrate AuthenticateController to use Use Cases
   - Migrate Microsoft authentication to use Use Cases
   - Add ValidateTokenMiddleware for API authentication
   - Update routes to use new middleware
   - Add comprehensive tests with 90%+ coverage
   - Security: Token validation, generic errors, rate limiting"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Security review passed
- [ ] Integration testing successful
- [ ] Commit successful

---

## Task 4.2: Migrate Authorization (Policies & Middleware)

**Estimated Time**: 3-4 ngày (24-32 giờ)

**Mục tiêu**: Migrate authorization mechanism sang sử dụng Use Cases từ IdentityAccess Context

### Bridge Services Strategy

**Context**: Trong quá trình migration, cần bridge services để link giữa DDD aggregates và Eloquent models hiện tại.

**Strategy**:
1. **Minimize Bridge Usage**: Chỉ sử dụng bridge khi absolutely necessary
2. **Isolate Bridges**: Bridges chỉ trong Infrastructure layer, không expose ra Domain/Application
3. **Document Clearly**: Document rõ ràng bridges là temporary
4. **Plan Removal**: Plan remove bridges trong Phase 6

**Bridge Services**:
- `UserIdentityBridgeService`: Link UserIdentity với User model
- Có thể cần thêm bridges cho Role, Permission nếu cần

**Removal Plan**:
- Phase 6: Remove bridge services sau khi:
  - All Controllers migrated
  - All Livewire components migrated
  - All API endpoints migrated
  - All tests passing
  - Production stable

---

### Task 4.2.1: Create Authorization Service

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tạo Authorization Service trong Application layer để centralize permission checks

**Note**: Authorization Service sẽ được sử dụng bởi:
- Policies (Laravel's authorization system)
- Middleware (route protection)
- Controllers (explicit permission checks)
- Livewire components (UI permission checks)

Đảm bảo service này centralize tất cả permission logic để dễ maintain và test.

#### Subtask 4.2.1.1: Design Authorization Service

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Review existing Policies để hiểu permission structure
2. [ ] Review `CheckPermissionUseCase` từ Phase 3
3. [ ] Design Authorization Service interface:
   - [ ] `can(string $permission): bool`
   - [ ] `canAny(array $permissions): bool`
   - [ ] `canAll(array $permissions): bool`
   - [ ] `hasRole(string $role): bool`
   - [ ] `hasAnyRole(array $roles): bool`
4. [ ] Document design decisions

**Verification**:
- [ ] Design completed
- [ ] Interface defined
- [ ] Documented

---

#### Subtask 4.2.1.2: Implement Authorization Service

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Tạo Authorization Service:
   ```bash
   mkdir -p app/IdentityAccess/Application/Services
   touch app/IdentityAccess/Application/Services/AuthorizationService.php
   ```
2. [ ] Implement service:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Application\Services;
   
   use App\IdentityAccess\Application\UseCases\CheckPermissionUseCase;
   use App\IdentityAccess\Domain\Aggregates\UserIdentity;
   use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
   use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
   
   /**
    * Authorization service.
    * Centralizes permission checks using Use Cases.
    */
   final class AuthorizationService
   {
       public function __construct(
           private readonly CheckPermissionUseCase $checkPermissionUseCase,
           private readonly UserIdentityRepositoryInterface $userIdentityRepository
       ) {
       }
       
       /**
        * Check if user has permission.
        */
       public function can(UserIdentityId $userIdentityId, string $permission): bool
       {
           $userIdentity = $this->userIdentityRepository->findById($userIdentityId);
           
           if ($userIdentity === null) {
               return false;
           }
           
           return $this->checkPermissionUseCase->execute($userIdentity, $permission);
       }
       
       /**
        * Check if user has any of the permissions.
        */
       public function canAny(UserIdentityId $userIdentityId, array $permissions): bool
       {
           foreach ($permissions as $permission) {
               if ($this->can($userIdentityId, $permission)) {
                   return true;
               }
           }
           
           return false;
       }
       
       /**
        * Check if user has all permissions.
        */
       public function canAll(UserIdentityId $userIdentityId, array $permissions): bool
       {
           foreach ($permissions as $permission) {
               if (!$this->can($userIdentityId, $permission)) {
                   return false;
               }
           }
           
           return true;
       }
   }
   ```
3. [ ] Write unit tests

**Code Review Checklist**:
- [ ] ✅ Service là `final`
- [ ] ✅ Dependencies injected via constructor
- [ ] ✅ Uses Use Cases
- [ ] ✅ Proper error handling
- [ ] ✅ Type hints đầy đủ

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Default deny** (returns false if user not found)
- [ ] ✅ **CRITICAL: Permission checks proper**
- [ ] ✅ **CRITICAL: No information leakage**
- [ ] ✅ Proper exception handling

**Verification**:
- [ ] Service implemented
- [ ] Unit tests pass
- [ ] Security review passed

---

#### Subtask 4.2.1.3: Create Helper để Bridge với Eloquent User

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo helper method để get UserIdentityId từ Eloquent User
2. [ ] Create facade hoặc helper class
3. [ ] Write tests

**Verification**:
- [ ] Helper created
- [ ] Tests pass

---

### Task 4.2.2: Refactor Policies

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Refactor Policies để sử dụng Authorization Service

#### Subtask 4.2.2.1: Refactor UserPolicy

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Update `UserPolicy`:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\Policies;
   
   use App\IdentityAccess\Application\Services\AuthorizationService;
   use App\Models\User;
   use Illuminate\Auth\Access\HandlesAuthorization;
   
   class UserPolicy
   {
       use HandlesAuthorization;
       
       public function __construct(
           private readonly AuthorizationService $authorizationService
       ) {
       }
       
       public function viewAny(User $user): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.view'
           );
       }
       
       public function view(User $user, User $model): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.view'
           );
       }
       
       public function create(User $user): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.create'
           );
       }
       
       public function update(User $user, User $model): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.edit'
           );
       }
       
       public function delete(User $user, User $model): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.delete'
           );
       }
       
       public function resetPassword(User $user, User $model): bool
       {
           return $this->authorizationService->can(
               $this->getUserIdentityId($user),
               'user.reset_password'
           );
       }
       
       private function getUserIdentityId(User $user): UserIdentityId
       {
           // Bridge method to get UserIdentityId from User model
           // This is temporary until full migration
           return UserIdentityId::fromString((string) $user->id);
       }
   }
   ```
2. [ ] Write tests
3. [ ] Verify existing functionality still works

**Verification**:
- [ ] Policy refactored
- [ ] Tests pass
- [ ] Existing functionality works

---

#### Subtask 4.2.2.2: Refactor Other Policies

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Refactor `FacultyPolicy`
2. [ ] Refactor `DepartmentPolicy`
3. [ ] Refactor `ClientPolicy`
4. [ ] Refactor `RolePolicy`
5. [ ] Write tests cho mỗi policy

**Verification**:
- [ ] All policies refactored
- [ ] Tests pass
- [ ] Existing functionality works

---

#### Subtask 4.2.2.3: Commit Policies Refactoring

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Manual testing:
   - [ ] Test permission checks trong controllers
   - [ ] Test permission checks trong Livewire components
   - [ ] Test route protection
4. [ ] Commit

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

### Task 4.2.3: Refactor Authorization Middleware

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Refactor existing middleware hoặc tạo middleware mới để sử dụng Authorization Service

#### Subtask 4.2.3.1: Review Existing Middleware

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing middleware:
   ```bash
   ls -la app/Http/Middleware/
   ```
2. [ ] Check middleware usage trong routes
3. [ ] Document current middleware behavior

**Verification**:
- [ ] Existing middleware reviewed
- [ ] Usage documented

---

#### Subtask 4.2.3.2: Create CheckPermission Middleware

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Tạo middleware:
   ```bash
   touch app/IdentityAccess/Infrastructure/Http/Middleware/CheckPermissionMiddleware.php
   ```
2. [ ] Implement middleware:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Http\Middleware;
   
   use App\IdentityAccess\Application\Services\AuthorizationService;
   use Closure;
   use Illuminate\Http\Request;
   use Symfony\Component\HttpFoundation\Response;
   
   /**
    * Middleware to check user permissions.
    */
   final class CheckPermissionMiddleware
   {
       public function __construct(
           private readonly AuthorizationService $authorizationService
       ) {
       }
       
       /**
        * Handle an incoming request.
        */
       public function handle(Request $request, Closure $next, string $permission): Response
       {
           $user = $request->user();
           
           if ($user === null) {
               abort(401, 'Unauthenticated');
           }
           
           $userIdentityId = $this->getUserIdentityId($user);
           
           if (!$this->authorizationService->can($userIdentityId, $permission)) {
               abort(403, 'Insufficient permissions');
           }
           
           return $next($request);
       }
       
       private function getUserIdentityId($user)
       {
           // Bridge method
           return UserIdentityId::fromString((string) $user->id);
       }
   }
   ```
3. [ ] Register middleware
4. [ ] Write tests

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Default deny** (abort if no permission)
- [ ] ✅ **CRITICAL: Proper error responses** (403, not 401)
- [ ] ✅ **CRITICAL: No information leakage**
- [ ] ✅ Proper exception handling

**Verification**:
- [ ] Middleware created
- [ ] Registered correctly
- [ ] Tests pass
- [ ] Security review passed

---

#### Subtask 4.2.3.3: Create CheckRole Middleware

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Create CheckRole middleware tương tự CheckPermission
2. [ ] Write tests
3. [ ] Register middleware

**Verification**:
- [ ] Middleware created
- [ ] Tests pass

---

#### Subtask 4.2.3.4: Update Routes

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Update routes để sử dụng middleware mới (nếu cần)
2. [ ] Test routes
3. [ ] Verify backward compatibility

**Verification**:
- [ ] Routes updated
- [ ] Routes work correctly
- [ ] Backward compatibility maintained

---

#### Subtask 4.2.3.5: Commit Middleware Refactoring

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Manual testing
4. [ ] Commit

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

### Task 4.2.4: Update Controllers và Livewire Components

**Estimated Time**: 0.5 ngày (4 giờ)

**Mục tiêu**: Update controllers và Livewire components để sử dụng Authorization Service (nếu cần)

#### Subtask 4.2.4.1: Review Controllers và Livewire Components

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Review controllers sử dụng `can()` method
2. [ ] Review Livewire components sử dụng `can()` method
3. [ ] Identify components cần update
4. [ ] Document changes needed

**Verification**:
- [ ] Components reviewed
- [ ] Changes documented

---

#### Subtask 4.2.4.2: Update Components (if needed)

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Update components nếu cần (thường không cần vì Policies handle)
2. [ ] Write tests
3. [ ] Verify functionality

**Note**: Thường không cần update vì Laravel's `can()` method tự động sử dụng Policies.

**Verification**:
- [ ] Components updated (if needed)
- [ ] Tests pass
- [ ] Functionality works

---

### Task 4.2.5: Cross-Context Integration Testing

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Test integration giữa OrganizationalStructure và IdentityAccess contexts

#### Subtask 4.2.5.1: Setup Integration Test Environment

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Create integration test base class:
   ```bash
   mkdir -p tests/Integration
   touch tests/Integration/CrossContextIntegrationTestCase.php
   ```
2. [ ] Implement base class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace Tests\Integration;
   
   use Illuminate\Foundation\Testing\RefreshDatabase;
   use Tests\TestCase;
   
   abstract class CrossContextIntegrationTestCase extends TestCase
   {
       use RefreshDatabase;
       
       protected function setUp(): void
       {
           parent::setUp();
           
           // Setup both contexts
           $this->setupOrganizationalStructure();
           $this->setupIdentityAccess();
       }
       
       protected function setupOrganizationalStructure(): void
       {
           // Setup OrganizationalStructure context
       }
       
       protected function setupIdentityAccess(): void
       {
           // Setup IdentityAccess context
       }
   }
   ```

**Verification**:
- [ ] Base class created
- [ ] Setup methods implemented

---

#### Subtask 4.2.5.2: Test User Creation Flow

**Estimated Time**: 2 giờ

**Mục tiêu**: Test end-to-end flow: Create User → UserIdentity created automatically

**Steps**:
1. [ ] Create integration test:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace Tests\Integration\CrossContext;
   
   use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
   use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
   use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
   use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
   use Tests\Integration\CrossContextIntegrationTestCase;
   
   class UserCreationFlowTest extends CrossContextIntegrationTestCase
   {
       public function test_user_creation_triggers_user_identity_creation(): void
       {
           // Create user trong OrganizationalStructure
           $dto = new CreateUserDTO(
               firstName: 'John',
               lastName: 'Doe',
               email: 'john@example.com',
           );
           
           $user = $this->createUserUseCase->execute($dto);
           
           // Verify UserIdentity được tạo trong IdentityAccess
           $userIdentityId = UserIdentityId::fromOrganizationalStructureUserId($user->id()->toString());
           $userIdentity = $this->userIdentityRepository->findById($userIdentityId);
           
           $this->assertNotNull($userIdentity);
           $this->assertEquals($user->email()->toString(), $userIdentity->email()->toString());
       }
   }
   ```
2. [ ] Run integration tests
3. [ ] Fix any issues

**Verification**:
- [ ] Integration test created
- [ ] Tests pass
- [ ] Flow works correctly

---

#### Subtask 4.2.5.3: Test Authentication Flow

**Estimated Time**: 2 giờ

**Mục tiêu**: Test end-to-end authentication flow

**Steps**:
1. [ ] Create integration test cho authentication:
   - [ ] User creation → UserIdentity creation
   - [ ] Authentication với credentials
   - [ ] Token issuance
   - [ ] Token validation
2. [ ] Test error scenarios:
   - [ ] Invalid credentials
   - [ ] UserIdentity not found
   - [ ] Rate limiting
3. [ ] Run tests

**Verification**:
- [ ] Authentication flow tested
- [ ] Error scenarios tested
- [ ] Tests pass

---

#### Subtask 4.2.5.4: Test Event Flow

**Estimated Time**: 2 giờ

**Mục tiêu**: Test Domain Events flow giữa contexts

**Steps**:
1. [ ] Test UserWasCreated event:
   - [ ] Event được publish từ OrganizationalStructure
   - [ ] Event được received bởi IdentityAccess listener
   - [ ] UserIdentity được tạo
2. [ ] Test event processing với Outbox Pattern:
   - [ ] Event được store trong outbox
   - [ ] Event được processed
   - [ ] Event được marked as processed
3. [ ] Test error handling:
   - [ ] Failed event processing
   - [ ] Retry mechanism
4. [ ] Run tests

**Verification**:
- [ ] Event flow tested
- [ ] Outbox pattern tested
- [ ] Error handling tested
- [ ] Tests pass

---

#### Subtask 4.2.5.5: Test Authorization Flow

**Estimated Time**: 1 giờ

**Mục tiêu**: Test authorization flow end-to-end

**Steps**:
1. [ ] Test permission checks:
   - [ ] User creation → Role assignment → Permission check
   - [ ] Permission check trong Policy
   - [ ] Permission check trong Middleware
   - [ ] Permission check trong Controller
2. [ ] Test cross-context authorization:
   - [ ] User từ OrganizationalStructure
   - [ ] Permissions từ IdentityAccess
   - [ ] Authorization checks work
3. [ ] Run tests

**Verification**:
- [ ] Authorization flow tested
- [ ] Cross-context authorization tested
- [ ] Tests pass

---

### Task 4.2.6: Commit Authorization Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run all tests (including integration tests)
2. [ ] Check coverage (>= 90%)
3. [ ] Security review
4. [ ] Integration testing:
   - [ ] Test permission checks trong Policies
   - [ ] Test permission checks trong Middleware
   - [ ] Test route protection
   - [ ] Test authorization bypass attempts
   - [ ] Test cross-context integration
5. [ ] Final commit:
   ```bash
   git add app/IdentityAccess/Application/Services/
   git add app/Policies/
   git add app/IdentityAccess/Infrastructure/Http/Middleware/
   git add routes/
   git add tests/Integration/
   git add tests/
   git commit -m "feat(IdentityAccess): complete Authorization migration

   - Add AuthorizationService to centralize permission checks
   - Refactor Policies to use AuthorizationService
   - Add CheckPermission and CheckRole middleware
   - Update routes to use new middleware
   - Add cross-context integration tests
   - Add comprehensive tests with 90%+ coverage
   - Security: Default deny, proper error responses"
   ```

**Verification**:
- [ ] All tests pass (including integration tests)
- [ ] Coverage >= 90%
- [ ] Security review passed
- [ ] Integration testing successful
- [ ] Commit successful

---

## Milestones và Progress Tracking

### Milestone 4.1: Authentication Flow Migrated

**Estimated Time**: 3-4 ngày

**Progress Checklist**:
- [ ] ✅ AuthenticateController refactored và tested
- [ ] ✅ Microsoft authentication refactored và tested
- [ ] ✅ ValidateTokenMiddleware created và tested
- [ ] ✅ Routes updated
- [ ] ✅ Feature tests coverage >= 90%
- [ ] ✅ Security review passed

**Status**: ⏳ In Progress

---

### Milestone 4.2: Authorization Migrated

**Estimated Time**: 4-5 ngày

**Progress Checklist**:
- [ ] ✅ AuthorizationService created và tested
- [ ] ✅ All Policies refactored và tested
- [ ] ✅ Authorization middleware created và tested
- [ ] ✅ Cross-context integration tests created và tested
- [ ] ✅ Routes updated
- [ ] ✅ Feature tests coverage >= 90%
- [ ] ✅ Integration tests coverage >= 90%
- [ ] ✅ Security review passed

**Status**: ⏳ Pending

---

## Quick Reference Commands

### Development Commands

```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Feature/IdentityAccess/Http/Controllers/Auth/AuthenticateControllerTest.php

# Run with coverage
php artisan test --coverage --min=90

# Code formatting
./vendor/bin/pint app/IdentityAccess/

# List routes
php artisan route:list
```

### Security Testing Commands

```bash
# Test authentication
php artisan test --filter=AuthenticationTest

# Test authorization
php artisan test --filter=AuthorizationTest

# Test middleware
php artisan test --filter=MiddlewareTest
```

### Git Commands

```bash
# Create feature branch
git checkout -b feat/migrate-ddd-phase4

# Stage files
git add app/IdentityAccess/

# Commit
git commit -m "feat(IdentityAccess): <description>"

# Push
git push origin feat/migrate-ddd-phase4
```

---

## Troubleshooting Guide

### Issue: Authentication không hoạt động sau migration

**Symptoms**: Login fails hoặc không authenticate user

**Root Cause**: Bridge service không hoạt động đúng hoặc Use Case issue

**Solution**:
1. Check UserIdentityBridgeService implementation
2. Verify AuthenticateUserUseCase works correctly
3. Check session configuration
4. Verify routes are correct
5. Check logs for errors

**Prevention**: Write comprehensive integration tests

---

### Issue: Authorization checks fail

**Symptoms**: Users không thể access resources mặc dù có permission

**Root Cause**: AuthorizationService hoặc Policy issue

**Solution**:
1. Check AuthorizationService implementation
2. Verify CheckPermissionUseCase works
3. Check Policy registration
4. Verify UserIdentityId mapping
5. Check permission codes match

**Prevention**: Write comprehensive authorization tests

---

### Issue: Middleware không hoạt động

**Symptoms**: Routes không được protected

**Root Cause**: Middleware không được register hoặc applied

**Solution**:
1. Check middleware registration trong Kernel
2. Verify middleware applied trong routes
3. Check middleware parameters
4. Verify middleware logic

**Prevention**: Write middleware tests

---

### Issue: Token validation fails

**Symptoms**: API requests fail với 401

**Root Cause**: ValidateTokenUseCase issue hoặc token format

**Solution**:
1. Check ValidateTokenUseCase implementation
2. Verify token format
3. Check token expiration
4. Verify token signature
5. Check middleware application

**Prevention**: Write comprehensive token validation tests

---

## Security Best Practices Checklist

### Authentication

- [ ] ✅ Generic error messages (không leak info)
- [ ] ✅ Rate limiting implemented
- [ ] ✅ Secure session management
- [ ] ✅ CSRF protection enabled
- [ ] ✅ Input validation
- [ ] ✅ Proper exception handling

### Authorization

- [ ] ✅ Default deny (fail secure)
- [ ] ✅ Permission checks at multiple layers
- [ ] ✅ Proper error responses (403, not 401)
- [ ] ✅ No information leakage
- [ ] ✅ Audit logging for authorization decisions

### Token Management

- [ ] ✅ Token validation proper
- [ ] ✅ Token expiration checked
- [ ] ✅ Token scope validated
- [ ] ✅ Generic error messages
- [ ] ✅ Secure token storage

### General

- [ ] ✅ Input validation everywhere
- [ ] ✅ Output encoding
- [ ] ✅ Error handling secure
- [ ] ✅ Logging without sensitive data
- [ ] ✅ Security headers configured

---

## Common Questions & Answers

### Q: Có cần migrate tất cả Policies ngay không?

**A**: Không nhất thiết. Có thể migrate từng Policy một:
1. Migrate UserPolicy trước (most used)
2. Migrate các Policies khác sau
3. Test thoroughly sau mỗi migration

**Recommendation**: Migrate theo thứ tự usage frequency.

---

### Q: Làm sao đảm bảo backward compatibility?

**A**: 
1. Giữ old Policies trong thời gian transition
2. Update routes gradually
3. Test thoroughly trước khi remove old code
4. Có rollback plan

**Recommendation**: Maintain both versions trong 1-2 sprints.

---

### Q: Bridge service có cần thiết không?

**A**: 
- **Temporary**: Bridge service là temporary cho đến khi migration hoàn tất
- **Necessary**: Cần để link UserIdentity với User model hiện tại
- **Remove**: Sẽ remove sau khi full migration

**Recommendation**: Document bridge service là temporary và plan removal.

---

### Q: Test coverage bao nhiêu là đủ?

**A**: 
- Authentication: >= 90% (critical)
- Authorization: >= 90% (critical)
- Middleware: >= 85%

**Recommendation**: Focus vào quality hơn là số lượng. Test critical paths và edge cases.

---

## Summary

Phase 4 là phase tích hợp các Use Cases từ Phase 3 vào infrastructure layer, đảm bảo authentication và authorization hoạt động đúng cách trong toàn bộ application. Cần:

1. ✅ **Follow workflow**: Planning → Security Review → Implementation → Testing → Review
2. ✅ **Small commits**: Commit sau mỗi subtask
3. ✅ **TDD**: Write tests trước khi implement
4. ✅ **Security first**: Always consider security implications
5. ✅ **Backward compatibility**: Đảm bảo không break existing functionality
6. ✅ **Testing**: Unit tests, Feature tests, Integration tests, Security tests
7. ✅ **Documentation**: Update PHPDoc và comments

**Estimated Total Time**: 6-8 ngày làm việc (48-64 giờ)

**Success Criteria**:
- ✅ Authentication flow migrated và tested
- ✅ Authorization migrated và tested
- ✅ Test coverage >= 90%
- ✅ Security review passed
- ✅ Backward compatibility maintained
- ✅ Code quality standards met
- ✅ Documentation complete

**⚠️ REMEMBER**: Security is critical. Always review code với security lens và test thoroughly before production.

---

## Deployment Strategy

**⚠️ CRITICAL**: Phase 4 liên quan đến authentication flow và authorization - critical components. Deployment phải cẩn thận.

### Pre-Deployment Checklist

**Trước khi deploy Phase 4, đảm bảo**:

- [ ] ✅ **All tests pass** (100% pass rate, >= 90% coverage)
- [ ] ✅ **Security review completed** và approved
- [ ] ✅ **Code review completed**
- [ ] ✅ **Database backup created**
- [ ] ✅ **Staging environment tested** thoroughly
- [ ] ✅ **Feature flags configured** cho authentication/authorization changes
- [ ] ✅ **Rollback plan ready** và tested
- [ ] ✅ **Monitoring setup** với authentication/authorization alerts
- [ ] ✅ **Team notified** về deployment
- [ ] ✅ **User communication** (nếu có breaking changes)

---

### Deployment Steps

#### Step 1: Pre-Deployment (30 phút)

1. [ ] **Create deployment branch**
2. [ ] **Final verification**:
   ```bash
   php artisan test
   php artisan test --filter=AuthenticationTest
   php artisan test --filter=AuthorizationTest
   ```
3. [ ] **Database backup**
4. [ ] **Environment check**

---

#### Step 2: Staging Deployment (1 giờ)

1. [ ] **Deploy to staging**
2. [ ] **Staging testing**:
   - [ ] Test authentication flow
   - [ ] Test authorization checks
   - [ ] Test middleware
   - [ ] Test policies
   - [ ] Test API token validation
3. [ ] **Fix any issues**

---

#### Step 3: Production Deployment (1 giờ)

**Strategy**: Feature flags với gradual rollout

1. [ ] **Enable feature flags** (OFF initially):
   ```php
   'new_auth_flow' => env('FEATURE_NEW_AUTH_FLOW', false),
   'new_authorization' => env('FEATURE_NEW_AUTHORIZATION', false),
   ```

2. [ ] **Deploy code** (features OFF)

3. [ ] **Verify** old code works

4. [ ] **Gradual rollout**:
   - Day 1: Internal users
   - Day 2-3: Monitor
   - Day 4-5: 25% users
   - Day 6-7: 50% users
   - Day 8+: 100% users

5. [ ] **Monitor**:
   - Authentication success rate
   - Authorization checks
   - Token validation
   - Error rates

---

#### Step 4: Post-Deployment Verification (30 phút)

1. [ ] **Smoke tests**:
   - [ ] Login flow
   - [ ] Logout flow
   - [ ] Permission checks
   - [ ] API authentication
   - [ ] Middleware protection

2. [ ] **Check logs**:
   - [ ] Authentication logs
   - [ ] Authorization logs
   - [ ] Error logs

3. [ ] **Check monitoring**:
   - [ ] Error rate
   - [ ] Response time
   - [ ] Authentication metrics
   - [ ] Authorization metrics

---

### Rollback Procedures

**Nếu có issues**:

1. [ ] **Disable feature flags** immediately
2. [ ] **Verify** old code works
3. [ ] **Investigate** issues
4. [ ] **Fix** và redeploy

**Full rollback** (nếu critical):
1. [ ] Put in maintenance mode
2. [ ] Revert code
3. [ ] Revert migrations (if needed)
4. [ ] Restart application
5. [ ] Verify works

---

### Feature Flags Strategy

**Implementation**:

```php
// config/features.php
'new_auth_flow' => env('FEATURE_NEW_AUTH_FLOW', false),
'new_authorization' => env('FEATURE_NEW_AUTHORIZATION', false),
```

**Usage**:
```php
if (Feature::active('new_auth_flow')) {
    // Use new AuthenticateController
} else {
    // Use old AuthenticateController
}
```

**Rollout**:
- Week 1: Internal users
- Week 2: 25% users
- Week 3: 50% users
- Week 4: 100% users

---

### Monitoring Checklist

**After deployment**:
- [ ] ✅ Authentication success rate
- [ ] ✅ Authorization check success rate
- [ ] ✅ Token validation success rate
- [ ] ✅ Error rate
- [ ] ✅ Response time
- [ ] ✅ User feedback

---

### Deployment Timeline

**Recommended Timeline**:
- Day 1: Staging deployment và testing
- Day 2: Production deployment với feature flags OFF
- Day 3-4: Enable cho internal users
- Day 5-7: Gradually enable cho all users
- Day 8+: Full rollout
