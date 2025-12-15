# ADR 002: Bounded Contexts Structure

**Status**: Accepted  
**Date**: 2024-12-14  
**Deciders**: Architecture Team  
**Context**: Migration từ monolithic Laravel application sang Domain-Driven Design (DDD) architecture

## Context

Trong quá trình migration sang DDD, chúng ta cần xác định các Bounded Contexts và cách chúng tương tác với nhau. Hệ thống SSO cần quản lý:

1. **Authentication & Authorization**: User identities, roles, permissions, OAuth clients, tokens
2. **Organizational Structure**: User profiles, faculties, departments, organizational relationships

## Decision

Chúng ta sẽ chia hệ thống thành **2 Bounded Contexts chính**:

### 1. IdentityAccess Context

**Responsibility**: Authentication, Authorization, và RBAC (Role-Based Access Control)

**Domain Aggregates**:
- `UserIdentity`: User authentication credentials (username, password, email)
- `Role`: User roles (SuperAdmin, Teacher, Student, etc.)
- `Permission`: Permissions (role.create, user.edit, etc.)
- `Client`: OAuth clients (applications that can authenticate users)

**Domain Entities**:
- `AccessToken`: OAuth access tokens

**Key Features**:
- User authentication (username/password, Microsoft SSO)
- Role and permission management
- OAuth2 token issuance and validation
- Authorization checks (`AuthorizationService`)

**Database Tables**:
- `user_identities` (with UUID)
- `roles` (with UUID)
- `permissions` (with UUID)
- `clients` (with UUID)
- `oauth_access_tokens` (Laravel Passport)
- `role_user` (pivot table)
- `permission_role` (pivot table)

### 2. OrganizationalStructure Context

**Responsibility**: User profiles và organizational structure (faculties, departments)

**Domain Aggregates**:
- `User`: User profile (full name, phone, code, faculty, department)

**Domain Entities**:
- `Faculty`: Faculty/Khoa
- `Department`: Department/Bộ môn

**Key Features**:
- User profile management
- Faculty and department management
- Organizational relationships
- Policy-based authorization (`PolicyAuthorizationService`)

**Database Tables**:
- `users` (with UUID) - links to `user_identities` via UUID
- `faculties` (with UUID)
- `departments` (with UUID)

### 3. SharedKernel

**Responsibility**: Common components shared across contexts

**Components**:
- Value Objects: `Email`, `Uuid`, `Timestamp`, `Status`
- Domain Exceptions: `DomainException`, `EntityNotFoundException`, `InvalidArgumentException`
- Outbox Pattern: `OutboxEvent`, `OutboxEventRepositoryInterface`

## Rationale

### Tại sao tách thành 2 Contexts?

1. **Separation of Concerns**:
   - **IdentityAccess**: Focuses on "who can do what" (authentication, authorization)
   - **OrganizationalStructure**: Focuses on "who belongs to which organization" (profiles, structure)

2. **Different Lifecycles**:
   - User identity (credentials) có thể tồn tại độc lập với user profile
   - Organizational structure có thể thay đổi mà không ảnh hưởng đến authentication

3. **Scalability**:
   - Có thể scale và deploy các contexts độc lập
   - Có thể thay đổi authentication mechanism mà không ảnh hưởng đến organizational structure

4. **Team Ownership**:
   - IdentityAccess team có thể làm việc độc lập với OrganizationalStructure team
   - Giảm conflicts và dependencies

### Tại sao UserIdentity và User tách riêng?

**Problem**: Trong monolithic Laravel, `User` model thường chứa cả authentication credentials và profile information.

**Solution**: Tách thành:
- `UserIdentity` (IdentityAccess): Authentication credentials (username, password, email)
- `User` (OrganizationalStructure): Profile information (full name, phone, faculty, department)

**Benefits**:
- ✅ Clear separation: Authentication vs Profile
- ✅ Different aggregates: `UserIdentity` và `User` có different consistency boundaries
- ✅ Flexibility: Có thể có `UserIdentity` mà không có `User` profile (temporary accounts)
- ✅ Security: Authentication logic tách biệt với profile logic

**Linking**: `User` và `UserIdentity` được link qua UUID (deterministic UUID generation từ integer ID)

## Consequences

### Positive

- ✅ **Clear boundaries**: Mỗi context có responsibility rõ ràng
- ✅ **Independent evolution**: Có thể thay đổi một context mà không ảnh hưởng context khác
- ✅ **Team autonomy**: Teams có thể làm việc độc lập
- ✅ **Scalability**: Có thể scale và deploy độc lập
- ✅ **Testability**: Dễ test từng context riêng biệt

### Negative

- ⚠️ **Complexity**: Cần manage relationships giữa contexts (UserIdentity ↔ User)
- ⚠️ **Bridge services**: Cần bridge services để link contexts (temporary, sẽ remove sau Phase 6)
- ⚠️ **Data consistency**: Cần đảm bảo consistency giữa contexts (via events hoặc eventual consistency)

### Mitigation

- ✅ **Bridge services**: Temporary services để link contexts (marked as TEMPORARY, sẽ remove sau Phase 6)
- ✅ **Domain Events**: Sử dụng events để communicate giữa contexts
- ✅ **UUID linking**: Deterministic UUID generation để link `UserIdentity` và `User`
- ✅ **Documentation**: Document rõ ràng cách contexts tương tác

## Context Mapping

### IdentityAccess ↔ OrganizationalStructure

**Relationship**: **Customer-Supplier** (IdentityAccess is supplier, OrganizationalStructure is customer)

- **IdentityAccess** provides: Authentication, Authorization services
- **OrganizationalStructure** consumes: Uses `AuthorizationService` để check permissions

**Communication**:
- **Synchronous**: `AuthorizationService` calls (when checking permissions)
- **Asynchronous**: Domain Events (when user identity created, password changed, etc.)

### SharedKernel ↔ All Contexts

**Relationship**: **Shared Kernel**

- All contexts use SharedKernel components (Value Objects, Exceptions, Outbox Pattern)
- Changes to SharedKernel affect all contexts (need careful versioning)

## Alternatives Considered

### 1. Single Context (Monolithic)

**Rejected**: 
- Violates DDD principles
- Difficult to scale và maintain
- Tight coupling between authentication và organizational structure

### 2. Three Contexts (Identity, Access, Organization)

**Rejected**:
- Identity và Access quá tightly coupled
- Over-engineering cho hệ thống hiện tại
- Có thể split sau nếu cần

### 3. UserIdentity và User trong cùng Context

**Rejected**:
- Violates aggregate boundaries
- Authentication và Profile có different consistency requirements
- Difficult to scale independently

## Implementation Notes

### Context Boundaries

- **Database**: Mỗi context có own database tables (có thể share database nhưng separate schemas)
- **Code**: Mỗi context trong own namespace (`App\IdentityAccess\`, `App\OrganizationalStructure\`)
- **Dependencies**: Contexts không import trực tiếp từ context khác (chỉ qua interfaces)

### Cross-Context Communication

1. **Synchronous**: Via Application Services (e.g., `AuthorizationService`)
2. **Asynchronous**: Via Domain Events và Outbox Pattern
3. **Data Linking**: Via UUID (deterministic UUID generation)

### Bridge Services (TEMPORARY)

- `UserIdentityBridgeService`: Links `UserIdentity` (IdentityAccess) với `User` (OrganizationalStructure)
- **Status**: TEMPORARY, sẽ remove sau Phase 6 khi full migration hoàn tất
- **Purpose**: Facilitate gradual migration từ monolithic sang DDD

## References

- [Domain-Driven Design - Bounded Context](https://martinfowler.com/bliki/BoundedContext.html)
- [Domain-Driven Design - Context Mapping](https://www.domainlanguage.com/ddd/context-mapping/)
- [Strategic Design Patterns](https://www.domainlanguage.com/ddd/patterns/)
