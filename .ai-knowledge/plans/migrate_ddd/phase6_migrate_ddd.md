# Phase 6: Cleanup và Tối ưu - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## ⚠️ CRITICAL - ĐỌC TRƯỚC KHI BẮT ĐẦU

**🚨 WARNING**: Phase 6 là phase **cleanup và optimization** - xóa code cũ và tối ưu hóa hệ thống. Cần cực kỳ cẩn thận để không xóa nhầm code đang được sử dụng.

### 🔒 Safety First

**Mọi quyết định trong Phase 6 phải được đánh giá cẩn thận:**

1. ✅ **Verify trước khi xóa**: Luôn verify code không còn được sử dụng trước khi xóa
2. ✅ **Backup trước khi xóa**: Tạo backup/branch trước khi xóa code
3. ✅ **Test sau khi xóa**: Test thoroughly sau mỗi lần xóa code
4. ✅ **Gradual removal**: Xóa từng phần một, không xóa tất cả cùng lúc

### 📋 Mandatory Safety Requirements

**TRƯỚC KHI BẮT ĐẦU BẤT KỲ TASK NÀO:**

- [ ] ✅ Phase 1, 2, 3, 4, và 5 đã hoàn thành và merge vào main branch
- [ ] ✅ Tất cả tests pass
- [ ] ✅ Production deployment successful
- [ ] ✅ Đã có backup/branch để rollback nếu cần
- [ ] ✅ Đã verify code không còn được sử dụng
- [ ] ✅ Đã có approval từ team lead

**LUÔN LUÔN:**
- ✅ Verify code không còn được sử dụng
- ✅ Test sau mỗi lần xóa
- ✅ Commit sau mỗi phần cleanup
- ✅ Document changes

---

## Tổng quan

Phase 6 là phase cuối cùng của migration, tập trung vào **cleanup code cũ** và **tối ưu hóa hệ thống**. Đây là phase quan trọng để đảm bảo codebase sạch sẽ, maintainable, và performant sau khi migration hoàn tất.

### Mục tiêu Phase 6

1. ✅ Xóa code cũ không còn sử dụng (Eloquent models, Controllers, Livewire components)
2. ✅ Update namespace và imports trong toàn bộ codebase
3. ✅ Tạo documentation đầy đủ (README, ADR, API docs, Migration guide)
4. ✅ Tối ưu hóa performance (queries, caching, event processing)
5. ✅ Performance testing và optimization
6. ✅ Đảm bảo codebase clean và maintainable

### Thời gian ước tính

**Tổng thời gian**: 8-11 ngày làm việc (64-88 giờ)

**Phân bổ**:
- Task 6.1: Remove Old Code (2-3 ngày) - includes Bridge Services removal
- Task 6.2: Documentation (3-4 ngày) - includes API docs, Onboarding guide, Troubleshooting runbook
- Task 6.3: Performance Optimization (3-4 ngày) - includes Monitoring setup

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 6, đảm bảo:

### Setup Checklist

- [ ] ✅ Phase 1, 2, 3, 4, và 5 đã hoàn thành và merge vào main branch
- [ ] ✅ Tất cả tests pass (100% pass rate)
- [ ] ✅ Production deployment successful và stable
- [ ] ✅ Đã tạo backup branch: `backup/pre-cleanup`
- [ ] ✅ Đã có approval từ team lead và stakeholders
- [ ] ✅ Đã có rollback plan
- [ ] ✅ Đã setup monitoring để track performance

### Environment Verification

**Steps để verify environment**:
1. [ ] Check all phases completion:
   ```bash
   # Verify all contexts exist
   ls -la app/SharedKernel/
   ls -la app/OrganizationalStructure/
   ls -la app/IdentityAccess/
   ```
2. [ ] Run all tests:
   ```bash
   php artisan test
   # Should be 100% pass rate
   ```
3. [ ] Check production status:
   - [ ] Production deployment successful
   - [ ] No critical bugs
   - [ ] Performance acceptable
4. [ ] Create backup branch:
   ```bash
   git checkout -b backup/pre-cleanup
   git push origin backup/pre-cleanup
   git checkout main
   ```
5. [ ] Check Git branch:
   ```bash
   git branch  # Should be on main or feature branch
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ All phases completed
- [ ] ✅ Production stable
- [ ] ✅ Backup created
- [ ] ✅ Environment ready for cleanup

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (20-30 phút)
   - [ ] Đọc task description và requirements
   - [ ] Identify code/files cần xóa hoặc optimize
   - [ ] Verify code không còn được sử dụng
   - [ ] Plan removal/optimization strategy
   - [ ] Estimate time và risks

2. **Safety Check** (15-20 phút) - **CRITICAL**
   - [ ] Verify code không còn được sử dụng (grep, IDE search)
   - [ ] Check dependencies
   - [ ] Review impact analysis
   - [ ] Get approval if needed

3. **Backup** (10 phút)
   - [ ] Create backup/branch
   - [ ] Document current state

4. **Implementation** (varies)
   - [ ] Remove code/files (nếu cleanup)
   - [ ] Optimize code (nếu optimization)
   - [ ] Update references
   - [ ] Fix imports

5. **Testing** (varies) - **CRITICAL**
   - [ ] Run all tests
   - [ ] Manual testing
   - [ ] Integration testing
   - [ ] Performance testing (nếu optimization)

6. **Verification** (15-20 phút)
   - [ ] Verify no broken references
   - [ ] Verify tests pass
   - [ ] Verify functionality works
   - [ ] Check performance (nếu optimization)

7. **Documentation** (10-15 phút)
   - [ ] Document changes
   - [ ] Update README/docs
   - [ ] Update changelog

8. **Commit** (10 phút)
   - [ ] Stage files
   - [ ] Write meaningful commit message
   - [ ] Push to branch

9. **Review** (varies)
   - [ ] Self-review
   - [ ] Request code review
   - [ ] Address feedback

### Best Practices cho Cleanup

- ✅ **Verify before delete**: Always verify code không còn được sử dụng
- ✅ **Small commits**: Commit sau mỗi phần cleanup
- ✅ **Test after each change**: Test sau mỗi lần xóa/optimize
- ✅ **Document changes**: Document mọi thay đổi
- ✅ **Gradual removal**: Xóa từng phần một

---

## Task 6.1: Remove Old Code

**Estimated Time**: 2-3 ngày (16-24 giờ)

**Mục tiêu**: Xóa code cũ không còn sử dụng sau khi migration hoàn tất

### Task 6.1.1: Identify Old Code

**Estimated Time**: 2 giờ

**Mục tiêu**: Identify tất cả code cũ cần xóa

#### Subtask 6.1.1.1: Identify Old Eloquent Models

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] List all Eloquent models:
   ```bash
   ls -la app/Models/
   ```
2. [ ] Check which models đã được migrate:
   - [ ] `User` - migrated to `OrganizationalStructure\Domain\Aggregates\User`
   - [ ] `Faculty` - migrated to `OrganizationalStructure\Domain\Entities\Faculty`
   - [ ] `Department` - migrated to `OrganizationalStructure\Domain\Entities\Department`
   - [ ] `Role` - migrated to `IdentityAccess\Domain\Aggregates\Role`
   - [ ] `Permission` - migrated to `IdentityAccess\Domain\Aggregates\Permission`
   - [ ] `Client` - migrated to `IdentityAccess\Domain\Aggregates\Client`
3. [ ] Verify models không còn được sử dụng:
   ```bash
   # Search for usage
   grep -r "App\\Models\\User" app/ --exclude-dir=Models
   grep -r "App\\Models\\Faculty" app/ --exclude-dir=Models
   # etc.
   ```
4. [ ] Document models cần xóa:
   - [ ] Models đã migrate
   - [ ] Models không còn references
   - [ ] Models cần giữ lại (nếu có)

**Verification**:
- [ ] All models identified
- [ ] Usage verified
- [ ] List documented

---

#### Subtask 6.1.1.2: Identify Old Controllers

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] List all Controllers:
   ```bash
   find app/Http/Controllers -name "*Controller.php"
   ```
2. [ ] Check which Controllers đã được migrate:
   - [ ] `Admin\UserController` - migrated to `OrganizationalStructure\Infrastructure\Http\Controllers\UserController`
   - [ ] `Admin\FacultyController` - migrated to `OrganizationalStructure\Infrastructure\Http\Controllers\FacultyController`
   - [ ] `Auth\AuthenticateController` - migrated to `IdentityAccess\Infrastructure\Http\Controllers\Auth\AuthenticateController`
   - [ ] `Api\UserController` - migrated to `OrganizationalStructure\Infrastructure\Http\Controllers\Api\UserController`
   - [ ] `Api\FacultyController` - migrated to `OrganizationalStructure\Infrastructure\Http\Controllers\Api\FacultyController`
3. [ ] Verify Controllers không còn được sử dụng:
   ```bash
   # Check routes
   grep -r "Admin\\UserController" routes/
   grep -r "Auth\\AuthenticateController" routes/
   # etc.
   ```
4. [ ] Document Controllers cần xóa

**Verification**:
- [ ] All Controllers identified
- [ ] Routes checked
- [ ] List documented

---

#### Subtask 6.1.1.3: Identify Old Livewire Components

**Estimated Time**: 30 phút

**Steps**:
1. [ ] List all Livewire components:
   ```bash
   find app/Livewire -name "*.php"
   ```
2. [ ] Check which components đã được migrate
3. [ ] Verify components không còn được sử dụng
4. [ ] Document components cần xóa

**Verification**:
- [ ] All components identified
- [ ] Usage verified
- [ ] List documented

---

### Task 6.1.2: Remove Old Eloquent Models

**Estimated Time**: 4 giờ

**Mục tiêu**: Xóa Eloquent models cũ đã được migrate

#### Subtask 6.1.2.1: Create Backup và Verify

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Create backup branch:
   ```bash
   git checkout -b backup/before-model-cleanup
   git push origin backup/before-model-cleanup
   git checkout main
   ```
2. [ ] Final verification:
   - [ ] Run all tests
   - [ ] Verify no references to old models
   - [ ] Check database migrations (models might be needed for migrations)

**Important**: Models có thể cần giữ lại cho database migrations. Verify carefully.

**Verification**:
- [ ] Backup created
- [ ] Final verification done
- [ ] Ready to remove

---

#### Subtask 6.1.2.2: Remove Models Gradually

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Remove `User` model (nếu không cần cho migrations):
   ```bash
   # First, verify no references
   grep -r "App\\Models\\User" app/ --exclude-dir=Models
   grep -r "App\\Models\\User" routes/
   grep -r "App\\Models\\User" tests/
   
   # If no references, remove
   rm app/Models/User.php
   ```
2. [ ] Run tests:
   ```bash
   php artisan test
   ```
3. [ ] Fix any broken references
4. [ ] Commit:
   ```bash
   git add .
   git commit -m "refactor: remove old User model (migrated to DDD)"
   ```
5. [ ] Repeat for other models:
   - [ ] `Faculty`
   - [ ] `Department`
   - [ ] `Role`
   - [ ] `Permission`
   - [ ] `Client`

**Note**: Có thể cần giữ models cho database migrations. In that case, chỉ remove business logic, keep model structure minimal.

**Verification**:
- [ ] Models removed
- [ ] Tests pass
- [ ] No broken references
- [ ] Committed

---

### Task 6.1.3: Remove Old Controllers

**Estimated Time**: 3 giờ

**Mục tiêu**: Xóa Controllers cũ đã được migrate

#### Subtask 6.1.3.1: Verify và Backup

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Create backup branch
2. [ ] Final verification:
   - [ ] Check routes không còn reference old controllers
   - [ ] Verify no imports của old controllers
   - [ ] Run tests

**Verification**:
- [ ] Backup created
- [ ] Verification done
- [ ] Ready to remove

---

#### Subtask 6.1.3.2: Remove Controllers Gradually

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Remove `Admin\UserController`:
   ```bash
   # Verify no references
   grep -r "Admin\\UserController" routes/
   grep -r "Admin\\UserController" tests/
   
   # Remove
   rm app/Http/Controllers/Admin/UserController.php
   ```
2. [ ] Run tests
3. [ ] Fix any broken references
4. [ ] Commit
5. [ ] Repeat for other controllers:
   - [ ] `Admin\FacultyController`
   - [ ] `Admin\DepartmentController`
   - [ ] `Admin\RoleController`
   - [ ] `Admin\ClientController`
   - [ ] `Auth\AuthenticateController` (old)
   - [ ] `Api\UserController` (old)
   - [ ] `Api\FacultyController` (old)

**Verification**:
- [ ] Controllers removed
- [ ] Tests pass
- [ ] No broken references
- [ ] Committed

---

### Task 6.1.4: Remove Old Livewire Components

**Estimated Time**: 2 giờ

**Mục tiêu**: Xóa Livewire components cũ đã được migrate

**Steps**:
1. [ ] Verify components không còn được sử dụng
2. [ ] Remove components gradually
3. [ ] Run tests
4. [ ] Fix broken references
5. [ ] Commit

**Verification**:
- [ ] Components removed
- [ ] Tests pass
- [ ] Committed

---

### Task 6.1.5: Remove Bridge Services

**Estimated Time**: 2 giờ

**Mục tiêu**: Remove bridge services sau khi migration hoàn tất

#### Subtask 6.1.5.1: Verify Bridge Services Not Needed

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Verify bridge services không còn được sử dụng:
   ```bash
   grep -r "UserIdentityBridgeService" app/
   grep -r "BridgeService" app/
   ```
2. [ ] Check all references:
   - [ ] Controllers
   - [ ] Livewire components
   - [ ] API controllers
   - [ ] Tests
3. [ ] Document removal plan

**Verification**:
- [ ] Usage verified
- [ ] Removal plan documented

---

#### Subtask 6.1.5.2: Remove Bridge Services

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Remove bridge service files:
   ```bash
   rm app/IdentityAccess/Infrastructure/Services/UserIdentityBridgeService.php
   ```
2. [ ] Remove service provider bindings
3. [ ] Update code that used bridges (should be none if properly migrated)
4. [ ] Run tests
5. [ ] Commit:
   ```bash
   git add .
   git commit -m "refactor: remove bridge services after full migration

   - Remove UserIdentityBridgeService (no longer needed)
   - All code now uses DDD aggregates directly
   - Migration complete"
   ```

**Verification**:
- [ ] Bridge services removed
- [ ] Tests pass
- [ ] No broken references
- [ ] Committed

---

### Task 6.1.6: Update Namespaces và Imports

**Estimated Time**: 3 giờ

**Mục tiêu**: Update tất cả namespaces và imports trong codebase

#### Subtask 6.1.5.1: Find All Old Imports

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Search for old imports:
   ```bash
   grep -r "use App\\Models\\User" app/
   grep -r "use App\\Http\\Controllers\\Admin" app/
   grep -r "use App\\Livewire" app/
   # etc.
   ```
2. [ ] Document all files cần update
3. [ ] Create update plan

**Verification**:
- [ ] All old imports identified
- [ ] Files documented
- [ ] Plan created

---

#### Subtask 6.1.5.2: Update Imports Gradually

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Update imports trong từng file:
   ```php
   // Old
   use App\Models\User;
   use App\Http\Controllers\Admin\UserController;
   
   // New
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use App\OrganizationalStructure\Infrastructure\Http\Controllers\UserController;
   ```
2. [ ] Use IDE refactoring tools (nếu available)
3. [ ] Run tests after each batch
4. [ ] Commit after each batch

**Verification**:
- [ ] All imports updated
- [ ] Tests pass
- [ ] Committed

---

### Task 6.1.6: Commit Old Code Removal

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run all tests
2. [ ] Check for any remaining old code references
3. [ ] Final commit:
   ```bash
   git add .
   git commit -m "refactor: remove old code after DDD migration

   - Remove old Eloquent models (migrated to DDD aggregates/entities)
   - Remove old Controllers (migrated to Infrastructure layer)
   - Remove old Livewire components (migrated to Infrastructure layer)
   - Update all namespaces and imports
   - All tests passing"
   ```

**Verification**:
- [ ] All tests pass
- [ ] No old code references
- [ ] Commit successful

---

## Task 6.2: Documentation

**Estimated Time**: 2-3 ngày (16-24 giờ)

**Mục tiêu**: Tạo documentation đầy đủ cho codebase mới

### Task 6.2.1: Update README

**Estimated Time**: 2 giờ

**Mục tiêu**: Cập nhật README với cấu trúc mới

#### Subtask 6.2.1.1: Review Existing README

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Read existing README.md
2. [ ] Identify sections cần update:
   - [ ] Project structure
   - [ ] Architecture overview
   - [ ] Setup instructions
   - [ ] Development guidelines
3. [ ] Document changes needed

**Verification**:
- [ ] README reviewed
- [ ] Changes identified

---

#### Subtask 6.2.1.2: Update README

**Estimated Time**: 1.5 giờ

**Steps**:
1. [ ] Update project structure section:
   ```markdown
   ## Project Structure
   
   ```
   app/
   ├── SharedKernel/          # Shared components across contexts
   │   ├── Domain/
   │   └── Infrastructure/
   ├── OrganizationalStructure/ # User profiles, Faculty, Department management
   │   ├── Domain/
   │   ├── Application/
   │   └── Infrastructure/
   ├── IdentityAccess/         # Authentication, Authorization, RBAC
   │   ├── Domain/
   │   ├── Application/
   │   └── Infrastructure/
   └── Models/                 # Legacy models (for migrations only)
   ```
2. [ ] Update architecture overview
3. [ ] Update setup instructions
4. [ ] Update development guidelines
5. [ ] Add DDD conventions section

**Verification**:
- [ ] README updated
- [ ] All sections complete

---

### Task 6.2.2: Create Architecture Decision Records (ADR)

**Estimated Time**: 4 giờ

**Mục tiêu**: Tạo ADR để document các architectural decisions

#### Subtask 6.2.2.1: Setup ADR Structure

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Create ADR directory:
   ```bash
   mkdir -p docs/adr
   ```
2. [ ] Create ADR template
3. [ ] Document ADR format

**Verification**:
- [ ] ADR structure created
- [ ] Template ready

---

#### Subtask 6.2.2.2: Create Key ADRs

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Create ADR-001: Domain-Driven Design Adoption
2. [ ] Create ADR-002: Bounded Contexts Structure
3. [ ] Create ADR-003: Event-Driven Architecture
4. [ ] Create ADR-004: Outbox Pattern Implementation
5. [ ] Create ADR-005: Authentication và Authorization Strategy
6. [ ] Create ADR-006: API Design Decisions

**ADR Template**:
```markdown
# ADR-XXX: [Title]

## Status
[Proposed | Accepted | Deprecated | Superseded]

## Context
[Describe the issue motivating this decision]

## Decision
[Describe the change that we're proposing or have agreed to implement]

## Consequences
[Describe the consequences, both positive and negative]

## Alternatives Considered
[Describe alternative approaches considered]
```

**Verification**:
- [ ] Key ADRs created
- [ ] All decisions documented

---

### Task 6.2.3: Update API Documentation

**Estimated Time**: 3 giờ

**Mục tiêu**: Cập nhật API documentation với endpoints mới

**Steps**:
1. [ ] Review existing API documentation
2. [ ] Update API endpoints list
3. [ ] Update request/response examples
4. [ ] Update authentication section
5. [ ] Update authorization section
6. [ ] Add error handling section

**Verification**:
- [ ] API documentation updated
- [ ] All endpoints documented

---

### Task 6.2.4: Setup API Documentation Tool

**Estimated Time**: 2 giờ

**Mục tiêu**: Setup API documentation tool (Swagger/OpenAPI)

#### Subtask 6.2.4.1: Install và Configure Swagger/OpenAPI

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Install L5-Swagger:
   ```bash
   composer require darkaonline/l5-swagger
   php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
   ```
2. [ ] Configure Swagger:
   ```php
   // config/l5-swagger.php
   'paths' => [
       'docs' => base_path('docs'),
       'annotations' => base_path('app'),
   ],
   ```
3. [ ] Add annotations to API Controllers:
   ```php
   /**
    * @OA\Get(
    *     path="/api/users",
    *     summary="List users",
    *     tags={"Users"},
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/User")
    *         )
    *     )
    * )
    */
   public function index(Request $request): JsonResponse
   {
       // ...
   }
   ```
4. [ ] Generate documentation:
   ```bash
   php artisan l5-swagger:generate
   ```

**Verification**:
- [ ] Swagger installed
- [ ] Configured correctly
- [ ] Documentation generated

---

#### Subtask 6.2.4.2: Document All API Endpoints

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Add annotations cho tất cả API endpoints
2. [ ] Document request/response formats
3. [ ] Document error responses
4. [ ] Document authentication requirements
5. [ ] Generate và verify documentation

**Verification**:
- [ ] All endpoints documented
- [ ] Documentation complete
- [ ] Accessible tại `/api/documentation`

---

### Task 6.2.5: Create Data Migration Documentation

**Estimated Time**: 2 giờ

**Mục tiêu**: Document data migration strategy và results

**Steps**:
1. [ ] Document data migration process:
   - [ ] What data was migrated
   - [ ] How data was migrated
   - [ ] Validation results
   - [ ] Issues encountered và solutions
2. [ ] Create data migration runbook
3. [ ] Document rollback procedures
4. [ ] Add to migration guide

**Verification**:
- [ ] Data migration documented
- [ ] Runbook created
- [ ] Rollback procedures documented

---

### Task 6.2.6: Create Onboarding Guide

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo onboarding guide cho developers mới

**Steps**:
1. [ ] Create onboarding guide:
   ```bash
   touch docs/onboarding-guide.md
   ```
2. [ ] Document:
   - [ ] Project structure
   - [ ] Architecture overview
   - [ ] How to setup development environment
   - [ ] How to run tests
   - [ ] How to add new features
   - [ ] Common tasks:
     - How to add new Use Case
     - How to add new Aggregate
     - How to add new API endpoint
     - How to add new permission
   - [ ] Code examples
   - [ ] Best practices
3. [ ] Add to README

**Verification**:
- [ ] Onboarding guide created
- [ ] All sections complete
- [ ] Examples included

---

### Task 6.2.7: Create Troubleshooting Runbook

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo troubleshooting runbook cho common issues

**Steps**:
1. [ ] Create runbook:
   ```bash
   touch docs/troubleshooting-runbook.md
   ```
2. [ ] Document common issues:
   - [ ] Authentication issues
   - [ ] Authorization issues
   - [ ] Database issues
   - [ ] Event processing issues
   - [ ] API issues
   - [ ] Performance issues
3. [ ] Document solutions:
   - [ ] Step-by-step solutions
   - [ ] Commands to run
   - [ ] What to check
   - [ ] When to escalate
4. [ ] Add to documentation

**Verification**:
- [ ] Runbook created
- [ ] Common issues documented
- [ ] Solutions provided

---

### Task 6.2.8: Create Migration Guide

**Estimated Time**: 4 giờ

**Mục tiêu**: Tạo migration guide cho developers

**Steps**:
1. [ ] Create migration guide document:
   ```bash
   touch docs/migration-guide.md
   ```
2. [ ] Document migration overview
3. [ ] Document new architecture
4. [ ] Document how to work with new structure
5. [ ] Document common tasks:
   - [ ] How to add new Use Case
   - [ ] How to add new Aggregate
   - [ ] How to add new API endpoint
   - [ ] How to add new permission
6. [ ] Add code examples
7. [ ] Add troubleshooting section

**Verification**:
- [ ] Migration guide created
- [ ] All sections complete
- [ ] Examples included

---

### Task 6.2.5: Commit Documentation

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review all documentation
2. [ ] Commit:
   ```bash
   git add docs/
   git add README.md
   git commit -m "docs: add comprehensive documentation

   - Update README with new architecture
   - Add Architecture Decision Records (ADR)
   - Update API documentation
   - Add migration guide for developers"
   ```

**Verification**:
- [ ] Documentation complete
- [ ] Commit successful

---

## Task 6.3: Performance Optimization

**Estimated Time**: 3-4 ngày (24-32 giờ)

**Mục tiêu**: Tối ưu hóa performance của hệ thống

### Task 6.3.1: Optimize Repository Queries

**Estimated Time**: 1.5 ngày (12 giờ)

**Mục tiêu**: Tối ưu queries trong Repositories

#### Subtask 6.3.1.1: Analyze Query Performance

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Enable query logging:
   ```php
   DB::enableQueryLog();
   ```
2. [ ] Run typical operations
3. [ ] Analyze query logs:
   - [ ] Identify N+1 queries
   - [ ] Identify slow queries
   - [ ] Identify missing indexes
4. [ ] Document findings

**Verification**:
- [ ] Queries analyzed
- [ ] Issues identified
- [ ] Documented

---

#### Subtask 6.3.1.2: Fix N+1 Queries

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Identify N+1 queries trong Repositories
2. [ ] Add eager loading:
   ```php
   // Before
   $users = $this->userRepository->findAll();
   foreach ($users as $user) {
       $faculty = $user->faculty(); // N+1 query
   }
   
   // After
   $users = $this->userRepository->findAllWithRelations(['faculty', 'department']);
   ```
3. [ ] Update Repository methods
4. [ ] Test performance improvement
5. [ ] Commit

**Verification**:
- [ ] N+1 queries fixed
- [ ] Performance improved
- [ ] Tests pass
- [ ] Committed

---

#### Subtask 6.3.1.3: Add Database Indexes

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Identify missing indexes:
   - [ ] Foreign keys
   - [ ] Frequently queried columns
   - [ ] Search columns
2. [ ] Create migration:
   ```php
   Schema::table('users', function (Blueprint $table) {
       $table->index('faculty_id');
       $table->index('email');
       $table->index(['faculty_id', 'status']);
   });
   ```
3. [ ] Run migration
4. [ ] Test performance improvement
5. [ ] Commit

**Verification**:
- [ ] Indexes added
- [ ] Performance improved
- [ ] Committed

---

#### Subtask 6.3.1.4: Optimize Complex Queries

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Identify complex/slow queries
2. [ ] Optimize queries:
   - [ ] Use select() to limit columns
   - [ ] Use whereHas() efficiently
   - [ ] Use subqueries when appropriate
   - [ ] Use raw queries if needed (carefully)
3. [ ] Test performance
4. [ ] Commit

**Verification**:
- [ ] Queries optimized
- [ ] Performance improved
- [ ] Committed

---

### Task 6.3.2: Implement Caching Strategy

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Implement caching cho frequently accessed data

#### Subtask 6.3.2.1: Design Caching Strategy

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Identify data suitable for caching:
   - [ ] Roles và Permissions (rarely change)
   - [ ] Faculty và Department lists (rarely change)
   - [ ] User permissions (can be cached per user)
2. [ ] Design cache keys structure
3. [ ] Design cache invalidation strategy
4. [ ] Document strategy

**Verification**:
- [ ] Strategy designed
- [ ] Documented

---

#### Subtask 6.3.2.2: Implement Role và Permission Caching

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Implement cache trong RoleRepository:
   ```php
   public function findAll(): array
   {
       return Cache::remember('roles.all', 3600, function () {
           // Fetch from database
       });
   }
   ```
2. [ ] Implement cache invalidation khi roles change
3. [ ] Test caching
4. [ ] Commit

**Verification**:
- [ ] Caching implemented
- [ ] Invalidation works
- [ ] Tests pass
- [ ] Committed

---

#### Subtask 6.3.2.3: Implement User Permission Caching

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Implement cache trong CheckPermissionUseCase:
   ```php
   public function execute(UserIdentity $userIdentity, string $permission): bool
   {
       $cacheKey = "user.{$userIdentity->id()}.permissions";
       
       return Cache::remember($cacheKey, 1800, function () use ($userIdentity) {
           // Check permissions
       });
   }
   ```
2. [ ] Implement cache invalidation khi permissions change
3. [ ] Test caching
4. [ ] Commit

**Verification**:
- [ ] Caching implemented
- [ ] Invalidation works
- [ ] Tests pass
- [ ] Committed

---

#### Subtask 6.3.2.4: Implement Faculty và Department Caching

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Implement cache trong FacultyRepository và DepartmentRepository
2. [ ] Implement cache invalidation
3. [ ] Test caching
4. [ ] Commit

**Verification**:
- [ ] Caching implemented
- [ ] Tests pass
- [ ] Committed

---

### Task 6.3.3: Optimize Event Processing

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tối ưu hóa event processing

#### Subtask 6.3.3.1: Analyze Event Processing

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Review event listeners
2. [ ] Identify slow event handlers
3. [ ] Identify events that can be async
4. [ ] Document findings

**Verification**:
- [ ] Events analyzed
- [ ] Issues identified

---

#### Subtask 6.3.3.2: Make Events Async Where Appropriate

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Identify events suitable for async processing:
   - [ ] Email notifications
   - [ ] Audit logging
   - [ ] Analytics events
2. [ ] Make events async:
   ```php
   event(new UserWasCreated($userId))->onQueue('events');
   ```
3. [ ] Update event listeners
4. [ ] Test async processing
5. [ ] Commit

**Verification**:
- [ ] Events made async
- [ ] Processing works
- [ ] Committed

---

#### Subtask 6.3.3.3: Optimize Outbox Pattern Processing

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Review Outbox Pattern implementation
2. [ ] Optimize outbox processing:
   - [ ] Batch processing
   - [ ] Parallel processing
   - [ ] Error handling
3. [ ] Test optimization
4. [ ] Commit

**Verification**:
- [ ] Outbox optimized
- [ ] Tests pass
- [ ] Committed

---

### Task 6.3.4: Setup Production Monitoring

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Setup comprehensive monitoring cho production

#### Subtask 6.3.4.1: Setup Application Performance Monitoring (APM)

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Choose APM tool:
   - [ ] Laravel Telescope (development)
   - [ ] New Relic, Datadog, hoặc similar (production)
   - [ ] Self-hosted solution
2. [ ] Install và configure:
   ```bash
   # Example với Laravel Telescope
   composer require laravel/telescope
   php artisan telescope:install
   php artisan migrate
   ```
3. [ ] Configure monitoring:
   - [ ] Error tracking
   - [ ] Performance monitoring
   - [ ] Query monitoring
   - [ ] Request monitoring
4. [ ] Test monitoring

**Verification**:
- [ ] APM tool installed
- [ ] Configured correctly
- [ ] Monitoring works

---

#### Subtask 6.3.4.2: Setup Error Tracking

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Choose error tracking tool:
   - [ ] Sentry (recommended)
   - [ ] Bugsnag
   - [ ] Rollbar
2. [ ] Install và configure:
   ```bash
   composer require sentry/sentry-laravel
   php artisan sentry:publish --dsn=YOUR_DSN
   ```
3. [ ] Configure error tracking:
   - [ ] Error reporting
   - [ ] Performance monitoring
   - [ ] Release tracking
4. [ ] Test error tracking

**Verification**:
- [ ] Error tracking installed
- [ ] Configured correctly
- [ ] Test errors được track

---

#### Subtask 6.3.4.3: Setup Logging Aggregation

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Choose logging solution:
   - [ ] ELK Stack (Elasticsearch, Logstash, Kibana)
   - [ ] Cloud logging (AWS CloudWatch, Google Cloud Logging)
   - [ ] Simple file-based với log rotation
2. [ ] Configure logging:
   - [ ] Log aggregation
   - [ ] Log search
   - [ ] Log retention
3. [ ] Setup log analysis

**Verification**:
- [ ] Logging configured
- [ ] Logs aggregated
- [ ] Search works

---

#### Subtask 6.3.4.4: Setup Alerting

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Configure alerts:
   - [ ] Error rate alerts (> 1%)
   - [ ] Performance alerts (response time > 500ms)
   - [ ] Security alerts (failed logins, suspicious activity)
   - [ ] Database alerts (slow queries, connection issues)
2. [ ] Setup notification channels:
   - [ ] Email
   - [ ] Slack
   - [ ] PagerDuty (for critical)
3. [ ] Test alerts

**Verification**:
- [ ] Alerts configured
- [ ] Notifications work
- [ ] Alerts tested

---

### Task 6.3.5: Performance Testing

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Performance testing và benchmarking

#### Subtask 6.3.4.1: Setup Performance Testing Tools

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Install performance testing tools:
   - [ ] Laravel Telescope (for debugging)
   - [ ] Laravel Debugbar (for development)
   - [ ] Apache Bench hoặc similar
2. [ ] Configure tools
3. [ ] Create performance test scenarios

**Verification**:
- [ ] Tools installed
- [ ] Configured
- [ ] Scenarios created

---

#### Subtask 6.3.4.2: Run Performance Tests

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Run baseline performance tests:
   - [ ] API endpoint response times
   - [ ] Database query times
   - [ ] Memory usage
   - [ ] CPU usage
2. [ ] Document baseline metrics
3. [ ] Run tests after optimizations
4. [ ] Compare results
5. [ ] Document improvements

**Verification**:
- [ ] Tests run
- [ ] Metrics documented
- [ ] Improvements verified

---

#### Subtask 6.3.4.3: Create Performance Report

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Create performance report:
   - [ ] Baseline metrics
   - [ ] Optimized metrics
   - [ ] Improvements achieved
   - [ ] Recommendations
2. [ ] Document trong docs/
3. [ ] Commit

**Verification**:
- [ ] Report created
- [ ] Documented
- [ ] Committed

---

### Task 6.3.5: Commit Performance Optimization

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run all tests
2. [ ] Verify performance improvements
3. [ ] Final commit:
   ```bash
   git add .
   git commit -m "perf: optimize system performance

   - Optimize repository queries (fix N+1, add indexes)
   - Implement caching strategy (roles, permissions, faculties)
   - Optimize event processing (async events, outbox optimization)
   - Performance testing và benchmarking
   - Performance improvements: [X]% faster queries, [Y]% faster API responses"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Performance improved
- [ ] Commit successful

---

## Milestones và Progress Tracking

### Milestone 6.1: Old Code Removed

**Estimated Time**: 2-3 ngày

**Progress Checklist**:
- [ ] ✅ Old Eloquent models removed
- [ ] ✅ Old Controllers removed
- [ ] ✅ Old Livewire components removed
- [ ] ✅ Namespaces và imports updated
- [ ] ✅ All tests passing
- [ ] ✅ No broken references

**Status**: ⏳ In Progress

---

### Milestone 6.2: Documentation Complete

**Estimated Time**: 3-4 ngày

**Progress Checklist**:
- [ ] ✅ README updated
- [ ] ✅ ADRs created
- [ ] ✅ API documentation updated với Swagger/OpenAPI
- [ ] ✅ API documentation tool setup
- [ ] ✅ Onboarding guide created
- [ ] ✅ Troubleshooting runbook created
- [ ] ✅ Data migration documentation created
- [ ] ✅ Migration guide created
- [ ] ✅ All documentation reviewed

**Status**: ⏳ Pending

---

### Milestone 6.3: Performance Optimized & Monitoring Setup

**Estimated Time**: 4-5 ngày

**Progress Checklist**:
- [ ] ✅ Repository queries optimized
- [ ] ✅ Caching strategy implemented
- [ ] ✅ Event processing optimized
- [ ] ✅ Production monitoring setup (APM, Error tracking, Logging)
- [ ] ✅ Alerting configured
- [ ] ✅ Performance testing completed
- [ ] ✅ Performance improvements documented

**Status**: ⏳ Pending

---

## Quick Reference Commands

### Cleanup Commands

```bash
# Find old code references
grep -r "App\\Models\\User" app/ --exclude-dir=Models

# Find unused files
php artisan unused:files

# Check for broken imports
php artisan check:imports
```

### Performance Commands

```bash
# Enable query logging
DB::enableQueryLog();

# Clear cache
php artisan cache:clear

# Run performance tests
php artisan test --filter=PerformanceTest
```

### Documentation Commands

```bash
# Generate API docs (if using tool)
php artisan api:generate

# Check documentation
php artisan docs:check
```

### Git Commands

```bash
# Create backup branch
git checkout -b backup/pre-cleanup
git push origin backup/pre-cleanup

# Commit cleanup
git commit -m "refactor: remove old code after DDD migration"
```

---

## Troubleshooting Guide

### Issue: Tests fail after removing old code

**Symptoms**: Tests fail với "Class not found" errors

**Root Cause**: Old code still referenced somewhere

**Solution**:
1. Check test files for old imports
2. Update test files to use new classes
3. Check factories và seeders
4. Update all references

**Prevention**: Verify no references before removing code

---

### Issue: Performance degraded after optimization

**Symptoms**: System slower after optimization changes

**Root Cause**: Optimization introduced issues

**Solution**:
1. Revert optimization changes
2. Analyze what went wrong
3. Fix issues
4. Re-apply optimization carefully
5. Test thoroughly

**Prevention**: Test performance after each optimization

---

### Issue: Cache not invalidating properly

**Symptoms**: Stale data in cache

**Root Cause**: Cache invalidation logic incorrect

**Solution**:
1. Review cache invalidation logic
2. Add proper invalidation events
3. Test cache invalidation
4. Monitor cache behavior

**Prevention**: Write tests for cache invalidation

---

## Safety Checklist

### Before Removing Code

- [ ] ✅ Code không còn được sử dụng (verified với grep/IDE)
- [ ] ✅ No references trong routes
- [ ] ✅ No references trong tests
- [ ] ✅ No references trong config files
- [ ] ✅ Backup created
- [ ] ✅ Tests pass before removal

### After Removing Code

- [ ] ✅ All tests pass
- [ ] ✅ No broken references
- [ ] ✅ Functionality works
- [ ] ✅ Performance acceptable
- [ ] ✅ Changes documented

### Before Performance Optimization

- [ ] ✅ Baseline metrics documented
- [ ] ✅ Optimization plan reviewed
- [ ] ✅ Rollback plan ready
- [ ] ✅ Monitoring setup

### After Performance Optimization

- [ ] ✅ Performance improved
- [ ] ✅ All tests pass
- [ ] ✅ No regressions
- [ ] ✅ Metrics documented

---

## Common Questions & Answers

### Q: Có nên xóa tất cả old code ngay không?

**A**: **KHÔNG**. Nên xóa từng phần một:
1. Xóa một phần
2. Test thoroughly
3. Deploy và monitor
4. Xóa phần tiếp theo

**Recommendation**: Gradual removal với testing sau mỗi step.

---

### Q: Models có cần giữ lại cho migrations không?

**A**: Có thể cần. Models có thể cần cho:
- Database migrations
- Legacy data migration scripts
- Temporary bridges

**Recommendation**: Giữ models minimal (chỉ structure, no business logic) nếu cần cho migrations.

---

### Q: Làm sao đảm bảo không xóa nhầm code đang dùng?

**A**: 
1. Use IDE "Find Usages" feature
2. Use grep to search codebase
3. Check routes, configs, tests
4. Review với team
5. Test thoroughly

**Recommendation**: Always verify before delete, use multiple methods.

---

### Q: Performance optimization bao nhiêu là đủ?

**A**: 
- **API response time**: < 200ms for most endpoints
- **Database queries**: < 50ms for most queries
- **Cache hit rate**: > 80% for cached data

**Recommendation**: Optimize until performance meets requirements, then stop.

---

## Summary

Phase 6 là phase cuối cùng của migration, tập trung vào cleanup và optimization. Cần:

1. ✅ **Safety first**: Verify trước khi xóa, backup trước khi thay đổi
2. ✅ **Gradual removal**: Xóa từng phần một với testing
3. ✅ **Comprehensive documentation**: Document mọi thay đổi
4. ✅ **Performance optimization**: Optimize queries, caching, events
5. ✅ **Testing**: Test thoroughly sau mỗi change
6. ✅ **Monitoring**: Monitor performance và errors

**Estimated Total Time**: 7-10 ngày làm việc (56-80 giờ)

**Success Criteria**:
- ✅ Old code removed safely
- ✅ Documentation complete
- ✅ Performance optimized
- ✅ All tests passing
- ✅ Codebase clean và maintainable
- ✅ Performance improvements documented
- ✅ Migration complete

**⚠️ REMEMBER**: Cleanup và optimization là important nhưng safety comes first. Always verify, test, và backup before making changes.

---

## Final Migration Checklist

### Completion Verification

- [ ] ✅ Phase 1: Shared Kernel - Complete
- [ ] ✅ Phase 2: OrganizationalStructure - Complete
- [ ] ✅ Phase 3: IdentityAccess - Complete
- [ ] ✅ Phase 4: Authentication & Authorization Flow - Complete
- [ ] ✅ Phase 5: API Endpoints - Complete
- [ ] ✅ Phase 6: Cleanup & Optimization - Complete

### Code Quality

- [ ] ✅ All tests passing (100% pass rate)
- [ ] ✅ Test coverage >= 90%
- [ ] ✅ Code formatted (Pint)
- [ ] ✅ No linter errors
- [ ] ✅ No security vulnerabilities

### Documentation

- [ ] ✅ README updated
- [ ] ✅ ADRs created
- [ ] ✅ API documentation updated
- [ ] ✅ Migration guide created
- [ ] ✅ Code comments complete

### Performance

- [ ] ✅ Performance optimized
- [ ] ✅ Performance metrics documented
- [ ] ✅ Performance tests passing
- [ ] ✅ Monitoring setup

### Production Readiness

- [ ] ✅ Production deployment successful
- [ ] ✅ No critical bugs
- [ ] ✅ Performance acceptable
- [ ] ✅ Monitoring active
- [ ] ✅ Team trained on new architecture

---

**🎉 CONGRATULATIONS**: Migration to DDD Architecture hoàn tất! Hệ thống giờ đây có architecture sạch sẽ, maintainable, và performant.
