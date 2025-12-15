# ADR 005: Authentication & Authorization Strategy

**Status**: Accepted  
**Date**: 2024-12-14  
**Deciders**: Architecture Team  
**Context**: Design authentication và authorization strategy cho SSO system với DDD architecture

## Context

SSO system cần support:
1. **Authentication**: User login (username/password, Microsoft SSO)
2. **Authorization**: Permission checks (role-based và policy-based)
3. **OAuth2**: Token-based authentication cho API clients
4. **Multi-context**: IdentityAccess context (authentication) và OrganizationalStructure context (policies)

## Decision

Chúng ta sẽ implement **hybrid authorization strategy**:

1. **IdentityAccess Context**: Role-based authorization (`AuthorizationService`)
2. **OrganizationalStructure Context**: Policy-based authorization (`PolicyAuthorizationService`)
3. **OAuth2**: Laravel Passport cho token-based authentication
4. **Bridge Services**: Temporary services để link contexts (sẽ remove sau Phase 6)

## Authentication

### Authentication Methods

1. **Username/Password**: Traditional form-based authentication
2. **Microsoft SSO**: OAuth2 flow với Microsoft Azure AD
3. **API Tokens**: OAuth2 access tokens (Laravel Passport)

### Authentication Flow

**Web Authentication**:
1. User submits credentials → `AuthenticateController::login()`
2. Use Case validates credentials → `AuthenticateUserUseCase`
3. Laravel session created → User logged in
4. Domain event fired → `UserWasAuthenticated`

**Microsoft SSO**:
1. User clicks "Login with Microsoft" → Redirect to Azure AD
2. Azure AD callback → `AuthenticateController::handleSocialiteCallback()`
3. Use Case handles Microsoft user → `AuthenticateWithMicrosoftUseCase`
4. Laravel session created → User logged in

**API Authentication**:
1. Client requests token → `AuthenticateSSOController::issueToken()`
2. Use Case issues token → `IssueAccessTokenUseCase`
3. Laravel Passport generates JWT token → Returned to client
4. Client uses token → `auth:api` middleware validates token

### Token Management

**Laravel Passport**:
- **Personal Access Tokens**: For API clients
- **Token Expiration**: 15 days (configurable)
- **Token Revocation**: Via `RevokeTokenUseCase`
- **Token Validation**: Via `ValidateTokenUseCase`

## Authorization

### Two-Tier Authorization Strategy

#### Tier 1: IdentityAccess Context (Role-Based)

**Service**: `AuthorizationService`

**Purpose**: Check permissions based on roles

**Usage**: IdentityAccess operations (role management, permission checks)

**Example**:
```php
// Check if user has permission
if ($this->authorizationService->can($user, 'role.create')) {
    // Allow action
}
```

**Permissions**: String-based permissions (e.g., 'role.create', 'user.edit')

#### Tier 2: OrganizationalStructure Context (Policy-Based)

**Service**: `PolicyAuthorizationService`

**Purpose**: Check policies based on Laravel Gates/Policies

**Usage**: OrganizationalStructure operations (user management, faculty management)

**Example**:
```php
// Check if user can view faculty
if ($this->policyAuthorizationService->canView($user, $faculty)) {
    // Allow action
}
```

**Policies**: Model-based policies (e.g., `UserPolicy`, `FacultyPolicy`)

### Why Two Tiers?

1. **Separation of Concerns**:
   - **IdentityAccess**: Focuses on "who can do what" (roles, permissions)
   - **OrganizationalStructure**: Focuses on "who can access which resource" (policies)

2. **Different Authorization Models**:
   - **Role-based**: Simple permission strings (e.g., 'role.create')
   - **Policy-based**: Complex business rules (e.g., "can only edit users in same faculty")

3. **Context Boundaries**:
   - Each context has own authorization logic
   - Reduces coupling between contexts

### Authorization Flow

**Web Authorization**:
1. User requests resource → Middleware checks authorization
2. Policy/Authorization service checks permission → Returns true/false
3. If authorized → Allow access
4. If not authorized → Return 403 Forbidden

**API Authorization**:
1. Client sends request with token → `auth:api` middleware validates token
2. Controller checks authorization → Uses PolicyAuthorizationService
3. If authorized → Process request
4. If not authorized → Return 403 Forbidden

## Implementation

### AuthorizationService (IdentityAccess)

```php
final class AuthorizationService
{
    public function can(UserIdentity $userIdentity, string $permission): bool
    {
        $user = $this->bridgeService->getEloquentUser($userIdentity);
        
        // Check if user has permission through roles
        return $user->hasPermission($permission);
    }

    public function canAny(UserIdentity $userIdentity, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($userIdentity, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function canAll(UserIdentity $userIdentity, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($userIdentity, $permission)) {
                return false;
            }
        }
        return true;
    }
}
```

### PolicyAuthorizationService (OrganizationalStructure)

```php
final class PolicyAuthorizationService implements PolicyAuthorizationServiceInterface
{
    public function canViewAny($user, string $model): bool
    {
        return Gate::forUser($user)->allows('viewAny', $model);
    }

    public function canView($user, $model): bool
    {
        return Gate::forUser($user)->allows('view', $model);
    }

    public function canCreate($user, string $model): bool
    {
        return Gate::forUser($user)->allows('create', $model);
    }

    public function canUpdate($user, $model): bool
    {
        return Gate::forUser($user)->allows('update', $model);
    }

    public function canDelete($user, $model): bool
    {
        return Gate::forUser($user)->allows('delete', $model);
    }

    public function can($user, $model, string $ability): bool
    {
        return Gate::forUser($user)->allows($ability, $model);
    }
}
```

### Middleware

**CheckPermissionMiddleware** (IdentityAccess):
```php
final class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('api')->user();
        
        if (!$this->authorizationService->can($user, $permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
```

**CheckRoleMiddleware** (IdentityAccess):
```php
final class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::guard('api')->user();
        
        if (!$user->hasRole($role)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
```

## Rationale

### Tại sao chọn hybrid strategy?

1. **Flexibility**: Support cả role-based và policy-based authorization
2. **Context Boundaries**: Mỗi context có own authorization logic
3. **Laravel Integration**: Leverage Laravel's Gate/Policies system
4. **Gradual Migration**: Có thể migrate từ role-based sang policy-based gradually

### Tại sao không chỉ dùng Policies?

**Problem**: Policies require Laravel models, nhưng IdentityAccess context sử dụng DDD aggregates

**Solution**: Hybrid approach:
- **IdentityAccess**: Role-based (simple, string-based permissions)
- **OrganizationalStructure**: Policy-based (complex, model-based policies)

### Tại sao không chỉ dùng Roles?

**Problem**: Role-based authorization không đủ flexible cho complex business rules (e.g., "can only edit users in same faculty")

**Solution**: Policy-based authorization cho OrganizationalStructure context

## Consequences

### Positive

- ✅ **Flexibility**: Support cả role-based và policy-based authorization
- ✅ **Separation**: Each context has own authorization logic
- ✅ **Laravel Integration**: Leverage Laravel's Gate/Policies
- ✅ **Testability**: Easy to test với mocks

### Negative

- ⚠️ **Complexity**: Two authorization systems có thể confusing
- ⚠️ **Bridge Services**: Cần bridge services để link contexts (temporary)
- ⚠️ **Learning Curve**: Developers cần hiểu cả 2 systems

### Mitigation

- ✅ **Documentation**: Document rõ ràng khi nào dùng service nào
- ✅ **Conventions**: Clear conventions về authorization usage
- ✅ **Examples**: Provide examples trong documentation
- ✅ **Bridge Services**: Mark as TEMPORARY, sẽ remove sau Phase 6

## Alternatives Considered

### 1. Only Role-Based Authorization

**Rejected**: 
- Không đủ flexible cho complex business rules
- Policies tốt hơn cho resource-based authorization

### 2. Only Policy-Based Authorization

**Rejected**:
- Policies require Laravel models, không phù hợp với DDD aggregates
- Role-based đơn giản hơn cho simple permissions

### 3. Custom Authorization System

**Rejected**:
- Over-engineering
- Laravel's Gate/Policies đã đủ tốt
- Không cần reinvent the wheel

## Implementation Notes

### Service Registration

**IdentityAccessServiceProvider**:
```php
public function register(): void
{
    $this->app->singleton(AuthorizationService::class);
    $this->app->singleton(UserIdentityBridgeService::class);
}
```

**OrganizationalStructureServiceProvider**:
```php
public function register(): void
{
    $this->app->singleton(
        PolicyAuthorizationServiceInterface::class,
        PolicyAuthorizationService::class
    );
}
```

### Policy Registration

**AuthServiceProvider**:
```php
protected $policies = [
    \App\OrganizationalStructure\Infrastructure\Eloquent\User::class => \App\OrganizationalStructure\Infrastructure\Policies\UserPolicy::class,
    \App\OrganizationalStructure\Infrastructure\Eloquent\Faculty::class => \App\OrganizationalStructure\Infrastructure\Policies\FacultyPolicy::class,
    // ...
];
```

## References

- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Role-Based Access Control (RBAC)](https://en.wikipedia.org/wiki/Role-based_access_control)
- [Policy-Based Authorization](https://laravel.com/docs/authorization#creating-policies)
