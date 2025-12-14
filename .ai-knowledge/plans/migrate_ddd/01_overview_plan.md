# Kế hoạch Migrate sang Domain-Driven Design (DDD)

Tài liệu này mô tả chi tiết kế hoạch migrate hệ thống SSO hiện tại sang kiến trúc Domain-Driven Design (DDD) theo các nguyên tắc và quy ước đã được định nghĩa trong `.ai-knowledge/commons/`.

## Mục tiêu

- Tái cấu trúc codebase theo kiến trúc DDD với 3 lớp: Domain, Application, Infrastructure
- Phân chia hệ thống thành các Bounded Context: IdentityAccess, OrganizationalStructure
- Tạo Shared Kernel chứa các thành phần dùng chung
- Đảm bảo tính nhất quán và dễ bảo trì
- Giữ nguyên chức năng hiện tại trong quá trình migrate

## Phân tích hiện trạng

### Cấu trúc hiện tại

```
app/
├── Models/              # Eloquent Models (Active Record)
│   ├── User.php         # Chứa cả identity và profile
│   ├── Role.php         # Vai trò và phân quyền
│   ├── Client.php       # Ứng dụng OAuth
│   ├── Faculty.php      # Khoa
│   ├── Department.php   # Phòng ban
│   ├── Permission.php   # Quyền hạn
│   └── PermissionGroup.php
├── Http/Controllers/    # Controllers xử lý HTTP
│   ├── Admin/           # Admin controllers
│   ├── Api/             # API controllers
│   └── Auth/            # Authentication controllers
├── Livewire/            # Livewire components
├── Jobs/                # Background jobs
├── Policies/            # Authorization policies
└── Enums/               # Role, Status
```

### Vấn đề hiện tại

1. **Anemic Domain Model**: Models chỉ là data containers, logic nghiệp vụ nằm trong Controllers/Livewire
2. **Tight Coupling**: Controllers trực tiếp sử dụng Eloquent models
3. **Mixed Concerns**: User model chứa cả identity và profile information
4. **No Clear Boundaries**: Không có ranh giới rõ ràng giữa các module
5. **Business Logic Scattered**: Logic nghiệp vụ nằm rải rác trong Controllers, Livewire components

## Chiến lược Migrate

### Nguyên tắc

1. **Incremental Migration**: Migrate từng phần, không làm một lúc
2. **Backward Compatibility**: Giữ API và routes hiện tại hoạt động trong quá trình migrate
3. **Test-Driven**: Viết tests cho code mới trước khi migrate
4. **Feature Parity**: Đảm bảo tất cả chức năng hiện tại vẫn hoạt động

### Thứ tự ưu tiên

1. **Phase 1**: Tạo Shared Kernel và cơ sở hạ tầng
2. **Phase 2**: Migrate OrganizationalStructure Context (ít phụ thuộc nhất)
3. **Phase 3**: Migrate IdentityAccess Context
4. **Phase 4**: Migrate Authentication & Authorization
5. **Phase 5**: Migrate API endpoints
6. **Phase 6**: Cleanup và tối ưu

---

## Phase 1: Shared Kernel và Cơ sở hạ tầng

**Mục tiêu**: Tạo các thành phần dùng chung và infrastructure cơ bản

### 1.1. Tạo cấu trúc thư mục Shared Kernel

```
app/SharedKernel/
├── Domain/
│   ├── ValueObjects/
│   │   ├── Email.php
│   │   ├── Uuid.php
│   │   └── Timestamp.php
│   ├── Exceptions/
│   │   ├── DomainException.php
│   │   ├── EntityNotFoundException.php
│   │   └── InvalidArgumentException.php
│   └── Interfaces/
│       ├── EventDispatcherInterface.php
│       └── ClockInterface.php
└── Infrastructure/
    ├── EventDispatcher/
    │   └── LaravelEventDispatcher.php
    ├── Clock/
    │   ├── SystemClock.php
    │   └── FixedClock.php
    └── Providers/
        └── SharedKernelServiceProvider.php
```

**Tasks:**
- [ ] Tạo các Value Objects: Email, Uuid, Timestamp
- [ ] Tạo các Exceptions cơ bản
- [ ] Tạo các Interfaces: EventDispatcherInterface, ClockInterface
- [ ] Implement LaravelEventDispatcher và SystemClock
- [ ] Tạo và đăng ký SharedKernelServiceProvider
- [ ] Viết unit tests cho các Value Objects

**Ước tính**: 2-3 ngày

### 1.2. Tạo Outbox Pattern Infrastructure

```
app/SharedKernel/
└── Infrastructure/
    ├── Outbox/
    │   ├── OutboxEvent.php (Eloquent Model)
    │   └── OutboxEventRepository.php
    └── Console/
        └── ProcessOutboxCommand.php
```

**Tasks:**
- [ ] Tạo migration cho bảng `outbox_events`
- [ ] Tạo OutboxEvent model
- [ ] Tạo ProcessOutboxCommand
- [ ] Đăng ký command trong scheduler
- [ ] Viết tests cho outbox pattern

**Ước tính**: 1-2 ngày

---

## Phase 2: OrganizationalStructure Context

**Mục tiêu**: Migrate phần quản lý người dùng, khoa, phòng ban

### 2.1. Tạo cấu trúc Domain Layer

```
app/OrganizationalStructure/
├── Domain/
│   ├── Aggregates/
│   │   └── User.php (Profile aggregate)
│   ├── ValueObjects/
│   │   ├── UserId.php
│   │   ├── FullName.php
│   │   ├── StudentCode.php
│   │   └── PhoneNumber.php
│   ├── Entities/
│   │   ├── Faculty.php
│   │   └── Department.php
│   ├── Events/
│   │   ├── UserWasCreated.php
│   │   ├── UserProfileWasUpdated.php
│   │   ├── UserWasAssignedToFaculty.php
│   │   ├── UserWasAssignedToDepartment.php
│   │   ├── FacultyWasCreated.php
│   │   └── DepartmentWasCreated.php
│   ├── Repositories/
│   │   ├── UserRepositoryInterface.php
│   │   ├── FacultyRepositoryInterface.php
│   │   └── DepartmentRepositoryInterface.php
│   └── Exceptions/
│       ├── UserNotFoundException.php
│       └── UserAlreadyExistsException.php
```

**Tasks:**
- [ ] Tạo Value Objects: UserId, FullName, StudentCode, PhoneNumber
- [ ] Tạo User Aggregate (chỉ chứa profile, không có identity)
- [ ] Tạo Faculty và Department Entities
- [ ] Tạo Domain Events
- [ ] Tạo Repository Interfaces
- [ ] Tạo Domain Exceptions
- [ ] Viết unit tests cho Domain layer

**Ước tính**: 3-4 ngày

### 2.2. Tạo Application Layer

```
app/OrganizationalStructure/
└── Application/
    ├── DTOs/
    │   ├── CreateUserDTO.php
    │   ├── UpdateUserProfileDTO.php
    │   ├── UserDTO.php
    │   ├── CreateFacultyDTO.php
    │   └── CreateDepartmentDTO.php
    └── UseCases/
        ├── CreateUserUseCase.php
        ├── UpdateUserProfileUseCase.php
        ├── FindUserUseCase.php
        ├── AssignUserToFacultyUseCase.php
        ├── AssignUserToDepartmentUseCase.php
        ├── CreateFacultyUseCase.php
        ├── UpdateFacultyUseCase.php
        ├── CreateDepartmentUseCase.php
        ├── FindUsersByFacultyUseCase.php
        └── ImportUsersFromExcelUseCase.php
```

**Tasks:**
- [ ] Tạo các DTOs
- [ ] Tạo các Use Cases
- [ ] Implement transaction và outbox pattern trong Use Cases
- [ ] Viết feature tests cho Use Cases

**Ước tính**: 4-5 ngày

### 2.3. Tạo Infrastructure Layer

```
app/OrganizationalStructure/
└── Infrastructure/
    ├── Persistence/
    │   ├── EloquentUserRepository.php
    │   ├── EloquentFacultyRepository.php
    │   └── EloquentDepartmentRepository.php
    ├── Http/
    │   ├── Controllers/
    │   │   ├── UserController.php
    │   │   ├── FacultyController.php
    │   │   └── DepartmentController.php
    │   └── Livewire/
    │       ├── User/
    │       ├── Faculty/
    │       └── Department/
    └── Providers/
        └── OrganizationalStructureServiceProvider.php
```

**Tasks:**
- [ ] Implement Repository interfaces với Eloquent
- [ ] Refactor Controllers để sử dụng Use Cases
- [ ] Refactor Livewire components để sử dụng Use Cases
- [ ] Tạo Service Provider và đăng ký bindings
- [ ] Viết integration tests

**Ước tính**: 5-6 ngày

### 2.4. Migrate Import Students Feature

**Tasks:**
- [ ] Tạo ImportUsersFromExcelUseCase
- [ ] Refactor StudentsImport để sử dụng Use Case
- [ ] Refactor ImportStudentsJob
- [ ] Update ImportStudents Livewire component
- [ ] Viết tests

**Ước tính**: 2-3 ngày

---

## Phase 3: IdentityAccess Context

**Mục tiêu**: Migrate phần xác thực, phân quyền, và quản lý ứng dụng

### 3.1. Tạo cấu trúc Domain Layer

```
app/IdentityAccess/
├── Domain/
│   ├── Aggregates/
│   │   ├── UserIdentity.php (Identity aggregate)
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Client.php
│   │   └── AccessToken.php
│   ├── ValueObjects/
│   │   ├── UserIdentityId.php
│   │   ├── Username.php
│   │   ├── PasswordHash.php
│   │   └── ClientSecret.php
│   ├── Events/
│   │   ├── UserWasAuthenticated.php
│   │   ├── TokenWasIssued.php
│   │   ├── TokenWasRevoked.php
│   │   ├── RoleWasCreated.php
│   │   ├── PermissionWasAssignedToRole.php
│   │   └── ClientWasRegistered.php
│   ├── Repositories/
│   │   ├── UserIdentityRepositoryInterface.php
│   │   ├── RoleRepositoryInterface.php
│   │   ├── PermissionRepositoryInterface.php
│   │   ├── ClientRepositoryInterface.php
│   │   └── AccessTokenRepositoryInterface.php
│   ├── Services/
│   │   ├── PasswordHasherInterface.php
│   │   └── TokenGeneratorInterface.php
│   └── Exceptions/
│       ├── InvalidCredentialsException.php
│       ├── UserIdentityNotFoundException.php
│       └── InsufficientPermissionException.php
```

**Tasks:**
- [ ] Tạo UserIdentity Aggregate (chỉ chứa identity, không có profile)
- [ ] Tạo Role, Permission, Client Aggregates
- [ ] Tạo AccessToken Aggregate
- [ ] Tạo Domain Events
- [ ] Tạo Repository Interfaces
- [ ] Tạo Domain Services Interfaces
- [ ] Tạo Domain Exceptions
- [ ] Viết unit tests

**Ước tính**: 4-5 ngày

### 3.2. Tạo Application Layer

```
app/IdentityAccess/
└── Application/
    ├── DTOs/
    │   ├── AuthenticateUserDTO.php
    │   ├── CreateRoleDTO.php
    │   ├── AssignPermissionToRoleDTO.php
    │   ├── RegisterClientDTO.php
    │   └── IssueTokenDTO.php
    └── UseCases/
        ├── AuthenticateUserUseCase.php
        ├── AuthenticateWithMicrosoftUseCase.php
        ├── IssueAccessTokenUseCase.php
        ├── ValidateTokenUseCase.php
        ├── RevokeTokenUseCase.php
        ├── CreateRoleUseCase.php
        ├── AssignPermissionToRoleUseCase.php
        ├── RegisterClientUseCase.php
        ├── CheckPermissionUseCase.php
        └── CreateDefaultCredentialsUseCase.php
```

**Tasks:**
- [ ] Tạo các DTOs
- [ ] Tạo các Use Cases
- [ ] Implement authentication logic
- [ ] Implement token management
- [ ] Implement RBAC logic
- [ ] Viết feature tests

**Ước tính**: 5-6 ngày

### 3.3. Tạo Infrastructure Layer

```
app/IdentityAccess/
└── Infrastructure/
    ├── Persistence/
    │   ├── EloquentUserIdentityRepository.php
    │   ├── EloquentRoleRepository.php
    │   ├── EloquentPermissionRepository.php
    │   ├── EloquentClientRepository.php
    │   └── EloquentAccessTokenRepository.php
    ├── Http/
    │   ├── Controllers/
    │   │   ├── AuthenticateController.php
    │   │   ├── RoleController.php
    │   │   └── ClientController.php
    │   └── Livewire/
    │       ├── Role/
    │       └── Client/
    ├── Services/
    │   ├── LaravelPasswordHasher.php
    │   ├── PassportTokenGenerator.php
    │   └── MicrosoftAuthService.php
    ├── Listeners/
    │   └── CreateDefaultCredentialsWhenUserWasCreated.php
    └── Providers/
        └── IdentityAccessServiceProvider.php
```

**Tasks:**
- [ ] Implement Repository interfaces
- [ ] Implement PasswordHasher và TokenGenerator
- [ ] Refactor AuthenticateController
- [ ] Refactor Role và Client Controllers
- [ ] Refactor Livewire components
- [ ] Tạo Event Listeners
- [ ] Tạo Service Provider
- [ ] Viết integration tests

**Ước tính**: 6-7 ngày

---

## Phase 4: Authentication & Authorization

**Mục tiêu**: Migrate authentication flow và authorization middleware

### 4.1. Migrate Authentication Flow

**Tasks:**
- [ ] Refactor AuthenticateController để sử dụng AuthenticateUserUseCase
- [ ] Refactor Microsoft authentication để sử dụng AuthenticateWithMicrosoftUseCase
- [ ] Tạo middleware mới sử dụng ValidateTokenUseCase
- [ ] Update routes để sử dụng middleware mới
- [ ] Viết tests cho authentication flow

**Ước tính**: 3-4 ngày

### 4.2. Migrate Authorization (Policies & Middleware)

**Tasks:**
- [ ] Refactor Policies để sử dụng CheckPermissionUseCase
- [ ] Refactor Middleware (CheckPermission, CheckRole) để sử dụng Use Cases
- [ ] Tạo Authorization Service trong Application layer
- [ ] Update tất cả routes và controllers
- [ ] Viết tests

**Ước tính**: 3-4 ngày

---

## Phase 5: API Endpoints

**Mục tiêu**: Migrate API endpoints sang DDD

### 5.1. Migrate User API

**Tasks:**
- [ ] Refactor Api\UserController để sử dụng Use Cases từ OrganizationalStructure
- [ ] Tạo API Resources mới
- [ ] Update API routes
- [ ] Viết API tests

**Ước tính**: 2-3 ngày

### 5.2. Migrate Faculty API

**Tasks:**
- [ ] Refactor Api\FacultyController để sử dụng Use Cases từ OrganizationalStructure
- [ ] Update API Resources
- [ ] Viết API tests

**Ước tính**: 1-2 ngày

### 5.3. Migrate SSO Authentication API

**Tasks:**
- [ ] Refactor AuthenticateSSOController để sử dụng Use Cases từ IdentityAccess
- [ ] Update OAuth flow
- [ ] Viết API tests

**Ước tính**: 2-3 ngày

---

## Phase 6: Cleanup và Tối ưu

**Mục tiêu**: Dọn dẹp code cũ và tối ưu hóa

### 6.1. Remove Old Code

**Tasks:**
- [ ] Xóa các Eloquent models cũ (sau khi đã migrate xong)
- [ ] Xóa các Controllers cũ không còn sử dụng
- [ ] Xóa các Livewire components cũ
- [ ] Update namespace và imports

**Ước tính**: 2-3 ngày

### 6.2. Documentation

**Tasks:**
- [ ] Cập nhật README với cấu trúc mới
- [ ] Tạo ADR (Architecture Decision Records)
- [ ] Cập nhật API documentation
- [ ] Tạo migration guide cho developers

**Ước tính**: 2-3 ngày

### 6.3. Performance Optimization

**Tasks:**
- [ ] Tối ưu queries trong Repositories
- [ ] Implement caching strategy
- [ ] Optimize event processing
- [ ] Performance testing

**Ước tính**: 3-4 ngày

---

## Chi tiết từng Phase

### Phase 1: Shared Kernel - Chi tiết

#### Task 1.1.1: Tạo Email Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Email.php`

**Steps:**
1. Copy code từ `04-code-examples.md` section 4.1.1
2. Tạo unit tests
3. Verify với existing code sử dụng email

**Dependencies**: None

**Estimated Time**: 2-3 giờ

#### Task 1.1.2: Tạo Uuid Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Uuid.php`

**Steps:**
1. Copy code từ `04-code-examples.md` section 4.1.2
2. Install ramsey/uuid nếu chưa có
3. Tạo unit tests
4. Replace các string IDs trong codebase

**Dependencies**: ramsey/uuid package

**Estimated Time**: 3-4 giờ

#### Task 1.1.3: Tạo Timestamp Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Timestamp.php`

**Steps:**
1. Copy code từ `04-code-examples.md` section 4.1.3
2. Tạo unit tests
3. Verify với existing datetime usage

**Dependencies**: None

**Estimated Time**: 2-3 giờ

#### Task 1.2: Tạo Outbox Pattern

**Files**:
- `database/migrations/XXXX_create_outbox_events_table.php`
- `app/SharedKernel/Infrastructure/Outbox/OutboxEvent.php`
- `app/SharedKernel/Infrastructure/Console/ProcessOutboxCommand.php`

**Steps:**
1. Tạo migration cho `outbox_events` table
2. Tạo OutboxEvent Eloquent model
3. Tạo ProcessOutboxCommand
4. Đăng ký command trong `app/Console/Kernel.php`
5. Viết tests

**Dependencies**: Phase 1.1 (Interfaces)

**Estimated Time**: 1 ngày

---

### Phase 2: OrganizationalStructure - Chi tiết

#### Task 2.1.1: Tách User Model

**Current State**: `app/Models/User.php` chứa cả identity và profile

**Target State**: 
- `app/OrganizationalStructure/Domain/Aggregates/User.php` (Profile only)
- `app/IdentityAccess/Domain/Aggregates/UserIdentity.php` (Identity only)

**Steps:**
1. Phân tích các fields trong User model hiện tại
2. Xác định fields nào thuộc Profile (OrganizationalStructure)
3. Xác định fields nào thuộc Identity (IdentityAccess)
4. Tạo User Aggregate trong OrganizationalStructure
5. Tạo UserIdentity Aggregate trong IdentityAccess (sẽ làm ở Phase 3)

**Fields Mapping**:
- **Profile (OrganizationalStructure)**:
  - `first_name`, `last_name` → FullName Value Object
  - `email` → Email Value Object (from SharedKernel)
  - `phone` → PhoneNumber Value Object
  - `code` → StudentCode Value Object (nếu là student)
  - `faculty_id`, `department_id` → References to Faculty/Department
  - `status` → Status enum (có thể move to SharedKernel)

- **Identity (IdentityAccess)**:
  - `user_name` → Username Value Object
  - `password` → PasswordHash Value Object
  - `is_change_password` → Business logic
  - `is_only_login_ms` → Business logic
  - `email_verified_at` → Timestamp Value Object

**Dependencies**: Phase 1 (SharedKernel Value Objects)

**Estimated Time**: 1 ngày

#### Task 2.1.2: Tạo Faculty và Department Entities

**Current State**: `app/Models/Faculty.php`, `app/Models/Department.php`

**Steps:**
1. Tạo Faculty Entity trong Domain layer
2. Tạo Department Entity trong Domain layer
3. Tạo Domain Events: FacultyWasCreated, DepartmentWasCreated
4. Tạo Repository Interfaces
5. Viết unit tests

**Dependencies**: Phase 1 (SharedKernel)

**Estimated Time**: 1 ngày

#### Task 2.2.1: Migrate Create User Use Case

**Current State**: `app/Livewire/User/Create.php` có logic tạo user

**Steps:**
1. Tạo CreateUserDTO
2. Tạo CreateUserUseCase
3. Implement transaction và outbox pattern
4. Refactor Livewire component để sử dụng Use Case
5. Viết feature tests

**Dependencies**: Task 2.1.1, Phase 1.2 (Outbox)

**Estimated Time**: 1 ngày

#### Task 2.2.2: Migrate Import Students Feature

**Current State**: 
- `app/Jobs/ImportStudentsJob.php`
- `app/Imports/StudentsImport.php`
- `app/Livewire/Faculty/ImportStudents.php`

**Steps:**
1. Tạo ImportUsersFromExcelUseCase
2. Refactor StudentsImport để sử dụng Use Case
3. Refactor ImportStudentsJob
4. Refactor ImportStudents Livewire component
5. Viết tests

**Dependencies**: Task 2.2.1

**Estimated Time**: 2 ngày

---

### Phase 3: IdentityAccess - Chi tiết

#### Task 3.1.1: Tạo UserIdentity Aggregate

**Steps:**
1. Tạo UserIdentity Aggregate với các Value Objects
2. Implement authentication methods
3. Tạo Domain Events: UserWasAuthenticated
4. Tạo Repository Interface
5. Viết unit tests

**Dependencies**: Phase 1, Phase 2.1.1

**Estimated Time**: 1 ngày

#### Task 3.2.1: Migrate Authentication

**Current State**: `app/Http/Controllers/Auth/AuthenticateController.php`

**Steps:**
1. Tạo AuthenticateUserUseCase
2. Tạo AuthenticateWithMicrosoftUseCase
3. Refactor AuthenticateController để sử dụng Use Cases
4. Update routes nếu cần
5. Viết tests

**Dependencies**: Task 3.1.1

**Estimated Time**: 2 ngày

#### Task 3.2.2: Migrate RBAC (Role & Permission)

**Current State**: 
- `app/Models/Role.php`
- `app/Models/Permission.php`
- `app/Livewire/Role/`

**Steps:**
1. Tạo Role và Permission Aggregates
2. Tạo Use Cases: CreateRoleUseCase, AssignPermissionToRoleUseCase
3. Refactor RoleController và Livewire components
4. Viết tests

**Dependencies**: Phase 1

**Estimated Time**: 2 ngày

---

## Testing Strategy

### Unit Tests

- **Domain Layer**: Test tất cả Value Objects, Aggregates, Entities
- **Application Layer**: Test các Use Cases với mocked repositories
- **Infrastructure Layer**: Test Repository implementations với test database

### Feature Tests

- Test các Use Cases end-to-end
- Test các API endpoints
- Test các Livewire components

### Integration Tests

- Test giao tiếp giữa các Bounded Contexts
- Test Event handling
- Test Outbox pattern

## Migration Checklist

### Pre-Migration

- [ ] Review và approve architecture design
- [ ] Setup development environment
- [ ] Create feature branch: `feat/migrate-ddd`
- [ ] Setup CI/CD để chạy tests tự động

### During Migration

- [ ] Follow incremental approach
- [ ] Write tests trước khi implement
- [ ] Code review cho mỗi phase
- [ ] Update documentation khi cần

### Post-Migration

- [ ] Run full test suite
- [ ] Performance testing
- [ ] Security audit
- [ ] Update production deployment plan
- [ ] Training cho team members

## Risks và Mitigation

### Risk 1: Breaking Changes

**Impact**: High  
**Probability**: Medium  
**Mitigation**: 
- Giữ backward compatibility trong quá trình migrate
- Tạo adapter layer nếu cần
- Gradual rollout

### Risk 2: Performance Degradation

**Impact**: Medium  
**Probability**: Low  
**Mitigation**:
- Performance testing trong mỗi phase
- Optimize queries trong repositories
- Implement caching strategy

### Risk 3: Timeline Overrun

**Impact**: Medium  
**Probability**: Medium  
**Mitigation**:
- Break down tasks thành smaller chunks
- Regular progress review
- Adjust scope nếu cần

## Timeline Tổng thể

| Phase | Duration | Start | End |
|-------|----------|-------|-----|
| Phase 1: Shared Kernel | 3-5 ngày | Week 1 | Week 1 |
| Phase 2: OrganizationalStructure | 14-18 ngày | Week 2 | Week 3-4 |
| Phase 3: IdentityAccess | 15-20 ngày | Week 4 | Week 6-7 |
| Phase 4: Auth & Authorization | 6-8 ngày | Week 7 | Week 8 |
| Phase 5: API Endpoints | 5-8 ngày | Week 8 | Week 9 |
| Phase 6: Cleanup | 7-10 ngày | Week 9 | Week 10 |

**Total Estimated Time**: 50-69 ngày làm việc (10-14 tuần)

## Success Criteria

- [ ] Tất cả code được migrate sang DDD architecture
- [ ] Tất cả tests pass (unit, feature, integration)
- [ ] Performance không giảm so với hiện tại
- [ ] Code coverage >= 80%
- [ ] Documentation đầy đủ
- [ ] Team members hiểu và có thể maintain code mới

## Notes

- Kế hoạch này là living document, sẽ được update khi có thay đổi
- Mỗi phase nên có code review trước khi chuyển sang phase tiếp theo
- Nên có daily standup để track progress
- Nếu gặp vấn đề, pause và discuss trước khi tiếp tục
