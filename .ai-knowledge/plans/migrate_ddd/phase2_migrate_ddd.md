# Phase 2: OrganizationalStructure Context - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## Tổng quan

Phase 2 tập trung vào việc migrate phần quản lý người dùng (profile), khoa (faculty), và phòng ban (department) sang kiến trúc DDD. Đây là Bounded Context đầu tiên được migrate hoàn chỉnh, làm nền tảng cho các phases tiếp theo.

### Mục tiêu Phase 2

1. ✅ Tạo Domain Layer hoàn chỉnh cho OrganizationalStructure Context
2. ✅ Implement Application Layer với các Use Cases và DTOs
3. ✅ Migrate Infrastructure Layer (Repositories, Controllers, Livewire)
4. ✅ Migrate Import Students Feature sang DDD architecture
5. ✅ Đảm bảo backward compatibility với code hiện tại
6. ✅ Đảm bảo code quality cao với test coverage >= 90%

### Thời gian ước tính

**Tổng thời gian**: 14-18 ngày làm việc (112-144 giờ)

**Phân bổ**:
- Task 2.1: Domain Layer (3-4 ngày)
- Task 2.2: Application Layer (4-5 ngày)
- Task 2.3: Infrastructure Layer (5-6 ngày)
- Task 2.4: Data Migration Strategy (1 ngày)
- Task 2.5: Migrate Import Feature (2-3 ngày)

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 2, đảm bảo:

### Setup Checklist

- [ ] ✅ Phase 1 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã review và approve architecture design trong `.ai-knowledge/commons/02-architecture.md`
- [ ] ✅ Đã setup development environment (PHP 8.3, Laravel 12, Composer)
- [ ] ✅ Đã tạo feature branch: `feat/migrate-ddd-phase2`
- [ ] ✅ Đã install các dependencies cần thiết từ Phase 1
- [ ] ✅ Đã đọc và hiểu các conventions trong `.ai-knowledge/commons/03-conventions.md`
- [ ] ✅ Đã review code examples trong `.ai-knowledge/commons/04-code-examples.md`
- [ ] ✅ Đã hiểu rõ cấu trúc database hiện tại (users, faculties, departments tables)
- [ ] ✅ Đã có database backup
- [ ] ✅ Đã setup CI/CD để chạy tests tự động

### Environment Verification

**Steps để verify environment**:
1. [ ] Check Phase 1 completion:
   ```bash
   # Verify SharedKernel exists
   ls -la app/SharedKernel/Domain/ValueObjects/
   # Should see: Email.php, Uuid.php, Timestamp.php
   ```
2. [ ] Check PHP version:
   ```bash
   php -v  # Should be PHP 8.3.x
   ```
3. [ ] Check Laravel version:
   ```bash
   php artisan --version  # Should be Laravel 12.x
   ```
4. [ ] Check Git branch:
   ```bash
   git branch  # Should be on feat/migrate-ddd-phase2
   ```
5. [ ] Check database structure:
   ```bash
   php artisan db:show  # Verify tables: users, faculties, departments
   ```
6. [ ] Run existing tests:
   ```bash
   php artisan test
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ Phase 1 components available
- [ ] ✅ Environment ready for development

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (10-15 phút)
   - [ ] Đọc task description và requirements
   - [ ] Review code examples từ `.ai-knowledge/commons/04-code-examples.md`
   - [ ] Review existing code để hiểu business logic hiện tại
   - [ ] Xác định dependencies và prerequisites
   - [ ] Estimate time

2. **Setup** (15-20 phút)
   - [ ] Tạo files và folders cần thiết
   - [ ] Verify prerequisites
   - [ ] Setup test files structure

3. **Implementation** (varies)
   - [ ] Implement theo TDD (Test-Driven Development)
   - [ ] Write tests first (Red)
   - [ ] Implement code (Green)
   - [ ] Refactor (Refactor)
   - [ ] Follow DDD principles và conventions

4. **Testing** (varies)
   - [ ] Unit tests cho Domain Layer
   - [ ] Feature tests cho Application Layer
   - [ ] Integration tests cho Infrastructure Layer
   - [ ] Manual testing

5. **Code Quality** (15-20 phút)
   - [ ] Run Laravel Pint
   - [ ] Run PHPStan (nếu có)
   - [ ] Check test coverage
   - [ ] Review code

6. **Documentation** (10-15 phút)
   - [ ] Update PHPDoc comments
   - [ ] Add inline comments cho complex logic
   - [ ] Update architecture docs nếu cần

7. **Commit** (10-15 phút)
   - [ ] Stage files
   - [ ] Write meaningful commit message
   - [ ] Push to feature branch

8. **Review** (varies)
   - [ ] Self-review code
   - [ ] Request code review từ team
   - [ ] Address feedback

### Best Practices

- ✅ **Small commits**: Commit sau mỗi subtask hoàn thành
- ✅ **TDD**: Viết test trước khi implement
- ✅ **Single Responsibility**: Mỗi class/method chỉ làm một việc
- ✅ **DRY**: Don't Repeat Yourself
- ✅ **Type Safety**: Sử dụng strict types và type hints đầy đủ
- ✅ **Documentation**: Comment bằng tiếng Anh, giải thích logic

---

## Task 2.1: Tạo Domain Layer

**Estimated Time**: 3-4 ngày (24-32 giờ)

**Mục tiêu**: Tạo toàn bộ Domain Layer cho OrganizationalStructure Context, bao gồm Value Objects, Aggregates, Entities, Events, Repository Interfaces, và Domain Exceptions.

### Task 2.1.1: Tạo Value Objects

**Estimated Time**: 1 ngày (8 giờ)

#### Subtask 2.1.1.1: UserId Value Object

**Estimated Time**: 1.5 giờ

**Mục tiêu**: Tạo UserId Value Object sử dụng Uuid từ SharedKernel

**Steps**:
1. [ ] Tạo file structure:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/ValueObjects
   touch app/OrganizationalStructure/Domain/ValueObjects/UserId.php
   ```
2. [ ] Implement UserId class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\ValueObjects\Uuid;
   use InvalidArgumentException;
   
   /**
    * User ID value object.
    * Wraps Uuid from SharedKernel for type safety.
    */
   final class UserId
   {
       private Uuid $uuid;
       
       private function __construct(Uuid $uuid)
       {
           $this->uuid = $uuid;
       }
       
       public static function fromString(string $value): self
       {
           return new self(Uuid::fromString($value));
       }
       
       public static function generate(): self
       {
           return new self(Uuid::generate());
       }
       
       public function equals(UserId $other): bool
       {
           return $this->uuid->equals($other->uuid);
       }
       
       public function toString(): string
       {
           return $this->uuid->toString();
       }
       
       public function __toString(): string
       {
           return $this->toString();
       }
   }
   ```
3. [ ] Verify implementation:
   - [ ] Class là `final`
   - [ ] Constructor là `private`
   - [ ] Có static factory methods
   - [ ] Có `equals()` method
   - [ ] Implement `__toString()`

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Sử dụng Uuid từ SharedKernel
- [ ] ✅ Có factory methods: `fromString()`, `generate()`
- [ ] ✅ Có `equals()` method
- [ ] ✅ Type hints đầy đủ
- [ ] ✅ PHPDoc comments đầy đủ

**Verification**:
- [ ] Syntax check: `php -l app/OrganizationalStructure/Domain/ValueObjects/UserId.php`
- [ ] IDE không có errors/warnings

---

#### Subtask 2.1.1.2: FullName Value Object

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo FullName Value Object để quản lý tên đầy đủ (first name + last name)

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/OrganizationalStructure/Domain/ValueObjects/FullName.php
   ```
2. [ ] Implement FullName class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Full name value object.
    * Represents a person's full name (first name + last name).
    */
   final class FullName
   {
       private string $firstName;
       private string $lastName;
       
       private function __construct(string $firstName, string $lastName)
       {
           $this->validate($firstName, $lastName);
           $this->firstName = trim($firstName);
           $this->lastName = trim($lastName);
       }
       
       public static function fromParts(string $firstName, string $lastName): self
       {
           return new self($firstName, $lastName);
       }
       
       public function firstName(): string
       {
           return $this->firstName;
       }
       
       public function lastName(): string
       {
           return $this->lastName;
       }
       
       public function fullName(): string
       {
           return "{$this->lastName} {$this->firstName}";
       }
       
       public function equals(FullName $other): bool
       {
           return $this->firstName === $other->firstName
               && $this->lastName === $other->lastName;
       }
       
       public function __toString(): string
       {
           return $this->fullName();
       }
       
       private function validate(string $firstName, string $lastName): void
       {
           if (empty(trim($firstName))) {
               throw new InvalidArgumentException('First name cannot be empty');
           }
           
           if (empty(trim($lastName))) {
               throw new InvalidArgumentException('Last name cannot be empty');
           }
           
           if (strlen(trim($firstName)) > 255) {
               throw new InvalidArgumentException('First name cannot exceed 255 characters');
           }
           
           if (strlen(trim($lastName)) > 255) {
               throw new InvalidArgumentException('Last name cannot exceed 255 characters');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   ```bash
   mkdir -p tests/Unit/OrganizationalStructure/Domain/ValueObjects
   touch tests/Unit/OrganizationalStructure/Domain/ValueObjects/FullNameTest.php
   ```
4. [ ] Implement tests:
   - [ ] Test valid full name creation
   - [ ] Test empty first name throws exception
   - [ ] Test empty last name throws exception
   - [ ] Test name too long throws exception
   - [ ] Test equals method
   - [ ] Test fullName() returns correct format
   - [ ] Test trimming whitespace
   - [ ] Test __toString() method

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Validation logic trong private method
- [ ] ✅ Có factory method `fromParts()`
- [ ] ✅ Có getters: `firstName()`, `lastName()`, `fullName()`
- [ ] ✅ Có `equals()` method
- [ ] ✅ Format: "Last Name First Name"
- [ ] ✅ Exception messages rõ ràng

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%

---

#### Subtask 2.1.1.3: StudentCode Value Object

**Estimated Time**: 1.5 giờ

**Mục tiêu**: Tạo StudentCode Value Object để quản lý mã sinh viên

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/OrganizationalStructure/Domain/ValueObjects/StudentCode.php
   ```
2. [ ] Implement StudentCode class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Student code value object.
    * Represents a unique student identifier code.
    */
   final class StudentCode
   {
       private string $code;
       
       private function __construct(string $code)
       {
           $this->validate($code);
           $this->code = trim($code);
       }
       
       public static function fromString(string $code): self
       {
           return new self($code);
       }
       
       public function equals(StudentCode $other): bool
       {
           return $this->code === $other->code;
       }
       
       public function toString(): string
       {
           return $this->code;
       }
       
       public function __toString(): string
       {
           return $this->toString();
       }
       
       private function validate(string $code): void
       {
           if (empty(trim($code))) {
               throw new InvalidArgumentException('Student code cannot be empty');
           }
           
           if (strlen(trim($code)) > 50) {
               throw new InvalidArgumentException('Student code cannot exceed 50 characters');
           }
           
           // Optional: Add format validation if needed
           // Example: Must be alphanumeric
           if (!preg_match('/^[A-Za-z0-9]+$/', trim($code))) {
               throw new InvalidArgumentException('Student code must be alphanumeric');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   - [ ] Test valid student code creation
   - [ ] Test empty code throws exception
   - [ ] Test code too long throws exception
   - [ ] Test invalid format throws exception
   - [ ] Test equals method
   - [ ] Test trimming whitespace

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Validation logic trong private method
- [ ] ✅ Có factory method `fromString()`
- [ ] ✅ Có `equals()` method
- [ ] ✅ Format validation (nếu có business rule)
- [ ] ✅ Exception messages rõ ràng

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%

---

#### Subtask 2.1.1.4: PhoneNumber Value Object

**Estimated Time**: 1.5 giờ

**Mục tiêu**: Tạo PhoneNumber Value Object để quản lý số điện thoại (nullable)

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/OrganizationalStructure/Domain/ValueObjects/PhoneNumber.php
   ```
2. [ ] Implement PhoneNumber class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Phone number value object.
    * Represents a phone number (can be nullable).
    */
   final class PhoneNumber
   {
       private ?string $number;
       
       private function __construct(?string $number)
       {
           if ($number !== null) {
               $this->validate($number);
               $this->number = trim($number);
           } else {
               $this->number = null;
           }
       }
       
       public static function fromString(?string $number): self
       {
           return new self($number);
       }
       
       public static function empty(): self
       {
           return new self(null);
       }
       
       public function isEmpty(): bool
       {
           return $this->number === null;
       }
       
       public function equals(PhoneNumber $other): bool
       {
           return $this->number === $other->number;
       }
       
       public function toString(): ?string
       {
           return $this->number;
       }
       
       public function __toString(): string
       {
           return $this->number ?? '';
       }
       
       private function validate(string $number): void
       {
           if (empty(trim($number))) {
               throw new InvalidArgumentException('Phone number cannot be empty');
           }
           
           if (strlen(trim($number)) > 20) {
               throw new InvalidArgumentException('Phone number cannot exceed 20 characters');
           }
           
           // Optional: Add format validation
           // Example: Must start with + or 0, and contain only digits
           if (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', trim($number))) {
               throw new InvalidArgumentException('Phone number format is invalid');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   - [ ] Test valid phone number creation
   - [ ] Test null phone number creation
   - [ ] Test empty() factory method
   - [ ] Test isEmpty() method
   - [ ] Test empty string throws exception
   - [ ] Test phone number too long throws exception
   - [ ] Test invalid format throws exception
   - [ ] Test equals method
   - [ ] Test trimming whitespace

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Supports nullable phone number
- [ ] ✅ Validation logic trong private method
- [ ] ✅ Có factory methods: `fromString()`, `empty()`
- [ ] ✅ Có `isEmpty()` method
- [ ] ✅ Format validation (nếu có business rule)
- [ ] ✅ Exception messages rõ ràng

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%

---

#### Subtask 2.1.1.5: Commit Value Objects

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint:
   ```bash
   ./vendor/bin/pint app/OrganizationalStructure/Domain/ValueObjects/
   ```
2. [ ] Run all tests:
   ```bash
   php artisan test tests/Unit/OrganizationalStructure/Domain/ValueObjects/
   ```
3. [ ] Check coverage:
   ```bash
   php artisan test --coverage --min=95
   ```
4. [ ] Stage và commit:
   ```bash
   git add app/OrganizationalStructure/Domain/ValueObjects/
   git add tests/Unit/OrganizationalStructure/Domain/ValueObjects/
   git commit -m "feat(OrganizationalStructure): add Value Objects

   - Add UserId value object wrapping SharedKernel Uuid
   - Add FullName value object for first/last name
   - Add StudentCode value object with validation
   - Add PhoneNumber value object (nullable)
   - Add comprehensive unit tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Code formatted correctly
- [ ] Commit successful

---

### Task 2.1.2: Tạo User Aggregate

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tạo User Aggregate Root để quản lý user profile (không bao gồm authentication)

#### Subtask 2.1.2.1: Setup và Structure

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Tạo folder structure:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/Aggregates
   touch app/OrganizationalStructure/Domain/Aggregates/User.php
   ```
2. [ ] Review existing User model để hiểu business logic:
   ```bash
   # Review app/Models/User.php
   # Note: first_name, last_name, email, phone, code, faculty_id, department_id
   ```
3. [ ] Xác định các Value Objects cần thiết:
   - [ ] UserId (đã có)
   - [ ] FullName (đã có)
   - [ ] Email (từ SharedKernel)
   - [ ] PhoneNumber (đã có)
   - [ ] StudentCode (đã có)
   - [ ] FacultyId (cần tạo - đơn giản là int)
   - [ ] DepartmentId (cần tạo - đơn giản là int)

**Verification**:
- [ ] Folder structure created
- [ ] Existing code reviewed
- [ ] Dependencies identified

---

#### Subtask 2.1.2.2: Tạo FacultyId và DepartmentId Value Objects

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo simple Value Objects cho FacultyId và DepartmentId

**Steps**:
1. [ ] Tạo FacultyId:
   ```bash
   touch app/OrganizationalStructure/Domain/ValueObjects/FacultyId.php
   ```
2. [ ] Implement FacultyId:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Faculty ID value object.
    */
   final class FacultyId
   {
       private int $id;
       
       private function __construct(int $id)
       {
           if ($id <= 0) {
               throw new InvalidArgumentException('Faculty ID must be positive');
           }
           $this->id = $id;
       }
       
       public static function fromInt(int $id): self
       {
           return new self($id);
       }
       
       public function toInt(): int
       {
           return $this->id;
       }
       
       public function equals(FacultyId $other): bool
       {
           return $this->id === $other->id;
       }
   }
   ```
3. [ ] Tạo DepartmentId tương tự
4. [ ] Write unit tests cho cả hai

**Verification**:
- [ ] Both Value Objects created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

#### Subtask 2.1.2.3: Implement User Aggregate

**Estimated Time**: 4 giờ

**Mục tiêu**: Implement User Aggregate với đầy đủ business logic

**Steps**:
1. [ ] Review code example từ `.ai-knowledge/commons/04-code-examples.md`
2. [ ] Implement User class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Aggregates;
   
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\OrganizationalStructure\Domain\ValueObjects\FullName;
   use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
   use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\OrganizationalStructure\Domain\Events\UserWasCreated;
   use App\OrganizationalStructure\Domain\Events\UserProfileWasUpdated;
   use App\OrganizationalStructure\Domain\Events\UserWasAssignedToFaculty;
   use App\OrganizationalStructure\Domain\Events\UserWasAssignedToDepartment;
   
   /**
    * User aggregate root.
    * Represents a user profile (not authentication).
    */
   final class User
   {
       private array $domainEvents = [];
       
       private UserId $id;
       private FullName $fullName;
       private Email $email;
       private ?StudentCode $studentCode;
       private PhoneNumber $phoneNumber;
       private ?FacultyId $facultyId;
       private ?DepartmentId $departmentId;
       
       private function __construct(
           UserId $id,
           FullName $fullName,
           Email $email,
           ?StudentCode $studentCode = null,
           PhoneNumber $phoneNumber = null,
           ?FacultyId $facultyId = null,
           ?DepartmentId $departmentId = null
       ) {
           $this->id = $id;
           $this->fullName = $fullName;
           $this->email = $email;
           $this->studentCode = $studentCode;
           $this->phoneNumber = $phoneNumber ?? PhoneNumber::empty();
           $this->facultyId = $facultyId;
           $this->departmentId = $departmentId;
       }
       
       /**
        * Factory method to create a new user.
        */
       public static function create(
           UserId $id,
           FullName $fullName,
           Email $email,
           ?StudentCode $studentCode = null,
           PhoneNumber $phoneNumber = null,
           ?FacultyId $facultyId = null,
           ?DepartmentId $departmentId = null
       ): self {
           $user = new self(
               $id,
               $fullName,
               $email,
               $studentCode,
               $phoneNumber,
               $facultyId,
               $departmentId
           );
           
           $user->recordEvent(new UserWasCreated(
               $id->toString(),
               $email->toString(),
               $fullName->fullName()
           ));
           
           return $user;
       }
       
       /**
        * Update user profile.
        */
       public function updateProfile(
           FullName $fullName,
           Email $email,
           ?PhoneNumber $phoneNumber = null
       ): void {
           $this->fullName = $fullName;
           $this->email = $email;
           
           if ($phoneNumber !== null) {
               $this->phoneNumber = $phoneNumber;
           }
           
           $this->recordEvent(new UserProfileWasUpdated($this->id->toString()));
       }
       
       /**
        * Assign user to faculty.
        */
       public function assignToFaculty(FacultyId $facultyId): void
       {
           if ($this->facultyId !== null && $this->facultyId->equals($facultyId)) {
               return; // Already assigned
           }
           
           $this->facultyId = $facultyId;
           
           $this->recordEvent(new UserWasAssignedToFaculty(
               $this->id->toString(),
               $facultyId->toInt()
           ));
       }
       
       /**
        * Assign user to department.
        */
       public function assignToDepartment(DepartmentId $departmentId): void
       {
           if ($this->departmentId !== null && $this->departmentId->equals($departmentId)) {
               return; // Already assigned
           }
           
           $this->departmentId = $departmentId;
           
           $this->recordEvent(new UserWasAssignedToDepartment(
               $this->id->toString(),
               $departmentId->toInt()
           ));
       }
       
       // Getters
       public function id(): UserId { return $this->id; }
       public function fullName(): FullName { return $this->fullName; }
       public function email(): Email { return $this->email; }
       public function studentCode(): ?StudentCode { return $this->studentCode; }
       public function phoneNumber(): PhoneNumber { return $this->phoneNumber; }
       public function facultyId(): ?FacultyId { return $this->facultyId; }
       public function departmentId(): ?DepartmentId { return $this->departmentId; }
       
       /**
        * Record a domain event.
        */
       private function recordEvent(object $event): void
       {
           $this->domainEvents[] = $event;
       }
       
       /**
        * Pull and clear domain events.
        *
        * @return object[]
        */
       public function pullDomainEvents(): array
       {
           $events = $this->domainEvents;
           $this->domainEvents = [];
           return $events;
       }
   }
   ```
3. [ ] Verify implementation:
   - [ ] Class là `final`
   - [ ] Private constructor
   - [ ] Factory method `create()`
   - [ ] Business methods: `updateProfile()`, `assignToFaculty()`, `assignToDepartment()`
   - [ ] Domain events được record
   - [ ] Getters cho tất cả properties
   - [ ] `pullDomainEvents()` method

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Factory method `create()` với domain event
- [ ] ✅ Business methods có logic validation
- [ ] ✅ Domain events được record đúng cách
- [ ] ✅ Getters return Value Objects
- [ ] ✅ No setters (immutability)
- [ ] ✅ PHPDoc comments đầy đủ

**Verification**:
- [ ] Syntax check passes
- [ ] IDE không có errors

---

#### Subtask 2.1.2.4: Write Unit Tests cho User Aggregate

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo test file:
   ```bash
   mkdir -p tests/Unit/OrganizationalStructure/Domain/Aggregates
   touch tests/Unit/OrganizationalStructure/Domain/Aggregates/UserTest.php
   ```
2. [ ] Implement tests:
   - [ ] Test `create()` method creates user và records event
   - [ ] Test `updateProfile()` updates và records event
   - [ ] Test `assignToFaculty()` assigns và records event
   - [ ] Test `assignToFaculty()` không duplicate event nếu đã assigned
   - [ ] Test `assignToDepartment()` assigns và records event
   - [ ] Test `assignToDepartment()` không duplicate event nếu đã assigned
   - [ ] Test `pullDomainEvents()` returns và clears events
   - [ ] Test getters return correct values
   - [ ] Test multiple events được record đúng thứ tự

**Test Example**:
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\Aggregates;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
use App\SharedKernel\Domain\ValueObjects\Email;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_create_user_records_event(): void
    {
        $userId = UserId::generate();
        $fullName = FullName::fromParts('John', 'Doe');
        $email = Email::fromString('john@example.com');
        
        $user = User::create($userId, $fullName, $email);
        
        $events = $user->pullDomainEvents();
        
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasCreated::class, $events[0]);
    }
    
    // Add more tests...
}
```

**Verification**:
- [ ] All unit tests pass
- [ ] Test coverage >= 95%

---

#### Subtask 2.1.2.5: Commit User Aggregate

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Check coverage
4. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Domain/Aggregates/
   git add app/OrganizationalStructure/Domain/ValueObjects/FacultyId.php
   git add app/OrganizationalStructure/Domain/ValueObjects/DepartmentId.php
   git add tests/Unit/OrganizationalStructure/Domain/Aggregates/
   git commit -m "feat(OrganizationalStructure): add User aggregate

   - Add User aggregate root with profile management
   - Add FacultyId and DepartmentId value objects
   - Implement create(), updateProfile(), assignToFaculty(), assignToDepartment()
   - Add domain events: UserWasCreated, UserProfileWasUpdated, etc.
   - Add comprehensive unit tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Commit successful

---

### Task 2.1.3: Tạo Faculty và Department Entities

**Estimated Time**: 0.5 ngày (4 giờ)

**Mục tiêu**: Tạo Faculty và Department Entities (không phải Aggregate Root)

#### Subtask 2.1.3.1: Tạo Faculty Entity

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo folder structure:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/Entities
   touch app/OrganizationalStructure/Domain/Entities/Faculty.php
   ```
2. [ ] Review existing Faculty model để hiểu business logic
3. [ ] Implement Faculty entity:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Entities;
   
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\OrganizationalStructure\Domain\Events\FacultyWasCreated;
   use App\Enums\Status;
   
   /**
    * Faculty entity.
    */
   final class Faculty
   {
       private array $domainEvents = [];
       
       private FacultyId $id;
       private string $name;
       private Status $status;
       private ?string $description;
       
       private function __construct(
           FacultyId $id,
           string $name,
           Status $status,
           ?string $description = null
       ) {
           $this->id = $id;
           $this->name = trim($name);
           $this->status = $status;
           $this->description = $description ? trim($description) : null;
       }
       
       public static function create(
           FacultyId $id,
           string $name,
           Status $status = Status::Active,
           ?string $description = null
       ): self {
           $faculty = new self($id, $name, $status, $description);
           
           $faculty->recordEvent(new FacultyWasCreated(
               $id->toInt(),
               $name
           ));
           
           return $faculty;
       }
       
       public function update(string $name, ?string $description = null): void
       {
           $this->name = trim($name);
           $this->description = $description ? trim($description) : null;
       }
       
       public function activate(): void
       {
           $this->status = Status::Active;
       }
       
       public function deactivate(): void
       {
           $this->status = Status::Inactive;
       }
       
       // Getters
       public function id(): FacultyId { return $this->id; }
       public function name(): string { return $this->name; }
       public function status(): Status { return $this->status; }
       public function description(): ?string { return $this->description; }
       
       private function recordEvent(object $event): void
       {
           $this->domainEvents[] = $event;
       }
       
       public function pullDomainEvents(): array
       {
           $events = $this->domainEvents;
           $this->domainEvents = [];
           return $events;
       }
   }
   ```
4. [ ] Write unit tests

**Verification**:
- [ ] Faculty entity created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

#### Subtask 2.1.3.2: Tạo Department Entity

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/OrganizationalStructure/Domain/Entities/Department.php
   ```
2. [ ] Review existing Department model
3. [ ] Implement Department entity tương tự Faculty
4. [ ] Write unit tests

**Verification**:
- [ ] Department entity created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

### Task 2.1.4: Tạo Domain Events

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo tất cả Domain Events cho OrganizationalStructure Context

#### Subtask 2.1.4.1: Setup Events Structure

**Estimated Time**: 15 phút

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/Events
   ```

**Verification**:
- [ ] Folder created

---

#### Subtask 2.1.4.2: Implement User Events

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Tạo UserWasCreated event:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Events;
   
   /**
    * Event fired when a user is created.
    */
   final class UserWasCreated
   {
       public function __construct(
           public readonly string $userId,
           public readonly string $email,
           public readonly string $fullName
       ) {
       }
   }
   ```
2. [ ] Tạo UserProfileWasUpdated event
3. [ ] Tạo UserWasAssignedToFaculty event
4. [ ] Tạo UserWasAssignedToDepartment event

**Verification**:
- [ ] All user events created
- [ ] Events are immutable (readonly properties)

---

#### Subtask 2.1.4.3: Implement Faculty và Department Events

**Estimated Time**: 45 phút

**Steps**:
1. [ ] Tạo FacultyWasCreated event
2. [ ] Tạo DepartmentWasCreated event
3. [ ] Tạo UsersWereImported event (cho import feature)

**Verification**:
- [ ] All events created
- [ ] Events are immutable

---

### Task 2.1.5: Tạo Repository Interfaces

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo Repository Interfaces cho User, Faculty, và Department

#### Subtask 2.1.5.1: UserRepositoryInterface

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/Repositories
   touch app/OrganizationalStructure/Domain/Repositories/UserRepositoryInterface.php
   ```
2. [ ] Implement interface:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Repositories;
   
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
   use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
   
   /**
    * User repository interface.
    */
   interface UserRepositoryInterface
   {
       public function save(User $user): void;
       
       public function findById(UserId $id): ?User;
       
       public function findByEmail(Email $email): ?User;
       
       public function findByStudentCode(StudentCode $code): ?User;
       
       /**
        * @return User[]
        */
       public function findByFacultyId(int $facultyId): array;
       
       /**
        * @return User[]
        */
       public function findByDepartmentId(int $departmentId): array;
       
       public function delete(UserId $id): void;
   }
   ```

**Verification**:
- [ ] Interface created
- [ ] Methods defined với proper type hints

---

#### Subtask 2.1.5.2: FacultyRepositoryInterface và DepartmentRepositoryInterface

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Tạo FacultyRepositoryInterface
2. [ ] Tạo DepartmentRepositoryInterface
3. [ ] Define methods tương tự UserRepositoryInterface

**Verification**:
- [ ] Both interfaces created
- [ ] Methods properly defined

---

### Task 2.1.6: Tạo Domain Exceptions

**Estimated Time**: 30 phút

**Mục tiêu**: Tạo Domain Exceptions cho OrganizationalStructure Context

#### Subtask 2.1.6.1: Implement Exceptions

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Domain/Exceptions
   ```
2. [ ] Tạo UserNotFoundException:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Exceptions;
   
   use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;
   
   /**
    * Exception thrown when user is not found.
    */
   final class UserNotFoundException extends EntityNotFoundException
   {
       public static function withId(string $id): self
       {
           return new self("User with ID {$id} not found");
       }
       
       public static function withEmail(string $email): self
       {
           return new self("User with email {$email} not found");
       }
   }
   ```
3. [ ] Tạo UserAlreadyExistsException:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Domain\Exceptions;
   
   use App\SharedKernel\Domain\Exceptions\DomainException;
   
   /**
    * Exception thrown when user already exists.
    */
   final class UserAlreadyExistsException extends DomainException
   {
       public static function withEmail(string $email): self
       {
           return new self("User with email {$email} already exists");
       }
       
       public static function withStudentCode(string $code): self
       {
           return new self("User with student code {$code} already exists");
       }
   }
   ```

**Verification**:
- [ ] Exceptions created
- [ ] Extend từ SharedKernel exceptions
- [ ] Static factory methods

---

### Task 2.1.7: Database Migrations - UUID Support

**Estimated Time**: 2-3 giờ

**Mục tiêu**: Add UUID columns cho OrganizationalStructure tables theo UUID Migration Strategy

**⚠️ IMPORTANT**: Theo UUID Migration Strategy (`.ai-knowledge/migration-strategy/uuid-migration-strategy.md`), chúng ta sẽ:
- ✅ **Giữ nguyên** integer ID (vẫn là Primary Key)
- ✅ **Thêm mới** UUID column (additional identifier)
- ✅ **Không remove** integer ID - giữ cả 2 vĩnh viễn

#### Subtask 2.1.7.1: Review Database Schema và UUID Migration Strategy

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review UUID Migration Strategy document:
   - [ ] Đọc `.ai-knowledge/migration-strategy/uuid-migration-strategy.md`
   - [ ] Hiểu rõ approach: giữ cả integer ID và UUID
   - [ ] Review migration phases
2. [ ] Review existing database schema:
   ```bash
   php artisan db:show
   ```
3. [ ] Check if current schema supports DDD structure:
   - [ ] Users table structure (id, department_id, faculty_id)
   - [ ] Faculties table structure (id)
   - [ ] Departments table structure (id)
   - [ ] Foreign keys và relationships
4. [ ] Identify tables cần UUID columns:
   - [ ] Core tables: users, faculties, departments
   - [ ] Foreign key columns: users.department_id, users.faculty_id
5. [ ] Document migration plan

**Verification**:
- [ ] UUID Migration Strategy reviewed
- [ ] Schema reviewed
- [ ] Migration plan documented

**Reference**: Xem `.ai-knowledge/migration-strategy/uuid-migration-strategy.md` để hiểu chi tiết.

---

#### Subtask 2.1.7.2: Create UUID Migration

**Estimated Time**: 1.5-2 giờ

**Steps**:
1. [ ] Create migration để add UUID columns:
   ```bash
   php artisan make:migration add_uuid_columns_to_organizational_structure_tables
   ```
2. [ ] Implement migration theo UUID Migration Strategy:
   ```php
   public function up(): void
   {
       // Add UUID column to core tables (nullable initially)
       Schema::table('users', function (Blueprint $table): void {
           $table->uuid('uuid')->nullable()->after('id');
           $table->index('uuid');
       });

       Schema::table('faculties', function (Blueprint $table): void {
           $table->uuid('uuid')->nullable()->after('id');
           $table->index('uuid');
       });

       Schema::table('departments', function (Blueprint $table): void {
           $table->uuid('uuid')->nullable()->after('id');
           $table->index('uuid');
       });

       // Add UUID columns for foreign keys
       Schema::table('users', function (Blueprint $table): void {
           $table->uuid('department_uuid')->nullable()->after('department_id');
           $table->uuid('faculty_uuid')->nullable()->after('faculty_id');
           $table->index('department_uuid');
           $table->index('faculty_uuid');
       });
   }
   ```
3. [ ] Test migrations:
   ```bash
   php artisan migrate:fresh
   php artisan migrate:rollback
   php artisan migrate
   ```
4. [ ] Verify UUID columns được tạo và integer ID vẫn giữ nguyên
5. [ ] Commit migration

**Verification**:
- [ ] UUID migration created
- [ ] Migrations tested
- [ ] UUID columns và indexes verified
- [ ] Integer ID columns vẫn giữ nguyên (không bị thay đổi)

**Note**: 
- UUID columns ban đầu là `nullable` để có thể populate dần
- Integer ID vẫn là Primary Key, không thay đổi
- Sau khi populate UUIDs, sẽ make UUID `not null` và `unique` trong Task 2.4.3

---

### Task 2.1.8: Commit Domain Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint trên toàn bộ Domain Layer
2. [ ] Run all unit tests
3. [ ] Check coverage (>= 90%)
4. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Domain/
   git add database/migrations/ # if migrations created
   git add tests/Unit/OrganizationalStructure/Domain/
   git commit -m "feat(OrganizationalStructure): complete Domain Layer

   - Add User aggregate root with profile management
   - Add Faculty and Department entities
   - Add Value Objects: UserId, FullName, StudentCode, PhoneNumber, FacultyId, DepartmentId
   - Add Domain Events: UserWasCreated, UserProfileWasUpdated, etc.
   - Add Repository Interfaces: UserRepositoryInterface, FacultyRepositoryInterface, DepartmentRepositoryInterface
   - Add Domain Exceptions: UserNotFoundException, UserAlreadyExistsException
   - Add database migrations (if needed)
   - Add comprehensive unit tests with 90%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Code formatted correctly
- [ ] Migrations tested (if created)
- [ ] Commit successful

---

## Task 2.2: Tạo Application Layer

**Estimated Time**: 4-5 ngày (32-40 giờ)

**Mục tiêu**: Tạo Application Layer với DTOs và Use Cases

### Task 2.2.1: Tạo DTOs

**Estimated Time**: 1 ngày (8 giờ)

#### Subtask 2.2.1.1: CreateUserDTO

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Application/DTOs
   touch app/OrganizationalStructure/Application/DTOs/CreateUserDTO.php
   ```
2. [ ] Implement DTO:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Application\DTOs;
   
   /**
    * Data Transfer Object for creating a user.
    */
   final readonly class CreateUserDTO
   {
       public function __construct(
           public string $firstName,
           public string $lastName,
           public string $email,
           public ?string $studentCode = null,
           public ?string $phone = null,
           public ?int $facultyId = null,
           public ?int $departmentId = null
       ) {
       }
   }
   ```

**Verification**:
- [ ] DTO created
- [ ] Properties are readonly
- [ ] Type hints đầy đủ

---

#### Subtask 2.2.1.2: Các DTOs khác

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo UpdateUserProfileDTO
2. [ ] Tạo UserDTO (for responses)
3. [ ] Tạo CreateFacultyDTO
4. [ ] Tạo CreateDepartmentDTO
5. [ ] Tạo ImportUsersDTO (cho import feature)

**Verification**:
- [ ] All DTOs created
- [ ] Properties properly typed

---

### Task 2.2.2: Tạo Use Cases

**Estimated Time**: 3-4 ngày (24-32 giờ)

**Note**: Đảm bảo có đầy đủ CRUD Use Cases:
- CreateUserUseCase ✅
- UpdateUserProfileUseCase ✅
- FindUserUseCase ✅
- DeleteUserUseCase (nếu cần)
- SearchUsersUseCase (nếu cần)
- ListUsersUseCase với filters và pagination (nếu cần)

#### Subtask 2.2.2.1: CreateUserUseCase

**Estimated Time**: 3 giờ

**Mục tiêu**: Implement Use Case để tạo user mới

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Application/UseCases
   touch app/OrganizationalStructure/Application/UseCases/CreateUserUseCase.php
   ```
2. [ ] Review code example từ `.ai-knowledge/commons/04-code-examples.md`
3. [ ] Implement Use Case:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Application\UseCases;
   
   use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\OrganizationalStructure\Domain\ValueObjects\FullName;
   use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
   use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException;
   use App\SharedKernel\Infrastructure\EventDispatcher\EventDispatcherInterface;
   use Illuminate\Support\Facades\DB;
   
   /**
    * Use case for creating a new user.
    */
   final class CreateUserUseCase
   {
       public function __construct(
           private readonly UserRepositoryInterface $userRepository,
           private readonly EventDispatcherInterface $eventDispatcher
       ) {
       }
       
       public function execute(CreateUserDTO $dto): User
       {
           return DB::transaction(function () use ($dto) {
               $email = Email::fromString($dto->email);
               
               // Check if user already exists
               $existingUser = $this->userRepository->findByEmail($email);
               if ($existingUser !== null) {
                   throw UserAlreadyExistsException::withEmail($dto->email);
               }
               
               // Create user aggregate
               $userId = UserId::generate();
               $fullName = FullName::fromParts($dto->firstName, $dto->lastName);
               $studentCode = $dto->studentCode ? StudentCode::fromString($dto->studentCode) : null;
               $phoneNumber = $dto->phone ? PhoneNumber::fromString($dto->phone) : PhoneNumber::empty();
               $facultyId = $dto->facultyId ? FacultyId::fromInt($dto->facultyId) : null;
               $departmentId = $dto->departmentId ? DepartmentId::fromInt($dto->departmentId) : null;
               
               $user = User::create(
                   $userId,
                   $fullName,
                   $email,
                   $studentCode,
                   $phoneNumber,
                   $facultyId,
                   $departmentId
               );
               
               // Save user
               $this->userRepository->save($user);
               
               // Dispatch domain events via outbox pattern
               $events = $user->pullDomainEvents();
               foreach ($events as $event) {
                   $this->eventDispatcher->dispatch($event);
               }
               
               return $user;
           });
       }
   }
   ```
4. [ ] Write feature tests:
   - [ ] Test successful user creation
   - [ ] Test duplicate email throws exception
   - [ ] Test domain events are dispatched
   - [ ] Test transaction rollback on error

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Dependencies injected via constructor
- [ ] ✅ Transaction được sử dụng
- [ ] ✅ Business rules được validate
- [ ] ✅ Domain events được dispatch
- [ ] ✅ Exception handling đúng cách

**Verification**:
- [ ] Use Case implemented
- [ ] Feature tests pass
- [ ] Coverage >= 90%

---

#### Subtask 2.2.2.2: UpdateUserProfileUseCase

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Implement UpdateUserProfileUseCase
2. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.3: FindUserUseCase

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Implement FindUserUseCase
2. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.4: AssignUserToFacultyUseCase và AssignUserToDepartmentUseCase

**Estimated Time**: 2 giờ mỗi cái

**Steps**:
1. [ ] Implement AssignUserToFacultyUseCase
2. [ ] Implement AssignUserToDepartmentUseCase
3. [ ] Write feature tests cho cả hai

**Verification**:
- [ ] Both Use Cases implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.5: Faculty Use Cases

**Estimated Time**: 3 giờ

**Steps**:
1. [ ] Implement CreateFacultyUseCase
2. [ ] Implement UpdateFacultyUseCase
3. [ ] Write feature tests

**Verification**:
- [ ] Use Cases implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.6: Department Use Cases

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Implement CreateDepartmentUseCase
2. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.7: FindUsersByFacultyUseCase

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Implement FindUsersByFacultyUseCase
2. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

#### Subtask 2.2.2.8: Additional Use Cases (if needed)

**Estimated Time**: 2-4 giờ

**Mục tiêu**: Review và add missing Use Cases nếu cần

**Steps**:
1. [ ] Review business requirements
2. [ ] Identify missing Use Cases:
   - [ ] `DeleteUserUseCase` (nếu cần soft delete hoặc hard delete)
   - [ ] `SearchUsersUseCase` (với advanced filters)
   - [ ] `ListUsersUseCase` (với pagination và filters)
   - [ ] `UpdateUserUseCase` (nếu khác với UpdateUserProfileUseCase)
3. [ ] Implement missing Use Cases
4. [ ] Write feature tests

**Note**: Không nhất thiết phải có tất cả Use Cases ngay. Có thể add sau khi cần.

**Verification**:
- [ ] Missing Use Cases identified
- [ ] Implemented (if needed)
- [ ] Tests pass

---

### Task 2.2.3: Commit Application Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all feature tests
3. [ ] Check coverage
4. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Application/
   git add tests/Feature/OrganizationalStructure/
   git commit -m "feat(OrganizationalStructure): add Application Layer

   - Add DTOs: CreateUserDTO, UpdateUserProfileDTO, UserDTO, etc.
   - Add Use Cases: CreateUserUseCase, UpdateUserProfileUseCase, etc.
   - Implement transaction và outbox pattern trong Use Cases
   - Add comprehensive feature tests with 90%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Commit successful

---

## Task 2.3: Tạo Infrastructure Layer

**Estimated Time**: 5-6 ngày (40-48 giờ)

**Mục tiêu**: Implement Infrastructure Layer với Repositories, Controllers, Livewire components, và Service Provider

### Task 2.3.1: Implement Repository Implementations

**Estimated Time**: 1.5 ngày (12 giờ)

#### Subtask 2.3.1.1: EloquentUserRepository

**Estimated Time**: 4 giờ

**Mục tiêu**: Implement UserRepositoryInterface với Eloquent

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Persistence
   touch app/OrganizationalStructure/Infrastructure/Persistence/EloquentUserRepository.php
   ```
2. [ ] Review existing User model để hiểu database structure
3. [ ] Implement repository:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Infrastructure\Persistence;
   
   use App\Models\User as EloquentUser;
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\OrganizationalStructure\Domain\ValueObjects\FullName;
   use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
   use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use Illuminate\Support\Facades\DB;
   
   /**
    * Eloquent implementation of UserRepositoryInterface.
    */
   final class EloquentUserRepository implements UserRepositoryInterface
   {
       public function save(User $user): void
       {
           DB::transaction(function () use ($user) {
               $eloquentUser = EloquentUser::find($user->id()->toString());
               
               if ($eloquentUser === null) {
                   // Create new
                   $eloquentUser = new EloquentUser();
                   $eloquentUser->id = $user->id()->toString();
               }
               
               // Map aggregate to Eloquent model
               $eloquentUser->first_name = $user->fullName()->firstName();
               $eloquentUser->last_name = $user->fullName()->lastName();
               $eloquentUser->email = $user->email()->toString();
               $eloquentUser->code = $user->studentCode()?->toString();
               $eloquentUser->phone = $user->phoneNumber()->isEmpty() ? null : $user->phoneNumber()->toString();
               $eloquentUser->faculty_id = $user->facultyId()?->toInt();
               $eloquentUser->department_id = $user->departmentId()?->toInt();
               
               $eloquentUser->save();
           });
       }
       
       public function findById(UserId $id): ?User
       {
           $eloquentUser = EloquentUser::find($id->toString());
           
           if ($eloquentUser === null) {
               return null;
           }
           
           return $this->toDomain($eloquentUser);
       }
       
       public function findByEmail(Email $email): ?User
       {
           $eloquentUser = EloquentUser::where('email', $email->toString())->first();
           
           if ($eloquentUser === null) {
               return null;
           }
           
           return $this->toDomain($eloquentUser);
       }
       
       public function findByStudentCode(StudentCode $code): ?User
       {
           $eloquentUser = EloquentUser::where('code', $code->toString())->first();
           
           if ($eloquentUser === null) {
               return null;
           }
           
           return $this->toDomain($eloquentUser);
       }
       
       /**
        * @return User[]
        */
       public function findByFacultyId(int $facultyId): array
       {
           $eloquentUsers = EloquentUser::where('faculty_id', $facultyId)->get();
           
           return $eloquentUsers->map(fn ($user) => $this->toDomain($user))->toArray();
       }
       
       /**
        * @return User[]
        */
       public function findByDepartmentId(int $departmentId): array
       {
           $eloquentUsers = EloquentUser::where('department_id', $departmentId)->get();
           
           return $eloquentUsers->map(fn ($user) => $this->toDomain($user))->toArray();
       }
       
       public function delete(UserId $id): void
       {
           EloquentUser::destroy($id->toString());
       }
       
       /**
        * Map Eloquent model to Domain aggregate.
        */
       private function toDomain(EloquentUser $eloquentUser): User
       {
           $userId = UserId::fromString((string) $eloquentUser->id);
           $fullName = FullName::fromParts($eloquentUser->first_name, $eloquentUser->last_name);
           $email = Email::fromString($eloquentUser->email);
           $studentCode = $eloquentUser->code ? StudentCode::fromString($eloquentUser->code) : null;
           $phoneNumber = $eloquentUser->phone ? PhoneNumber::fromString($eloquentUser->phone) : PhoneNumber::empty();
           $facultyId = $eloquentUser->faculty_id ? FacultyId::fromInt($eloquentUser->faculty_id) : null;
           $departmentId = $eloquentUser->department_id ? DepartmentId::fromInt($eloquentUser->department_id) : null;
           
           // Use reflection or a factory method to reconstruct without triggering events
           // For now, we'll use a private constructor approach
           return User::create(
               $userId,
               $fullName,
               $email,
               $studentCode,
               $phoneNumber,
               $facultyId,
               $departmentId
           );
       }
   }
   ```
4. [ ] Write integration tests:
   - [ ] Test save() creates new user
   - [ ] Test save() updates existing user
   - [ ] Test findById() returns user
   - [ ] Test findByEmail() returns user
   - [ ] Test findByStudentCode() returns user
   - [ ] Test findByFacultyId() returns array of users
   - [ ] Test delete() removes user

**Code Review Checklist**:
- [ ] ✅ Implements UserRepositoryInterface
- [ ] ✅ Maps Eloquent model to Domain aggregate correctly
- [ ] ✅ Maps Domain aggregate to Eloquent model correctly
- [ ] ✅ Handles nullable fields correctly
- [ ] ✅ Uses transactions where needed
- [ ] ✅ No business logic in repository

**Note**: Cần thêm method `fromDomain()` hoặc sử dụng reflection để reconstruct User aggregate từ database mà không trigger events.

**Solution**: Implement factory method hoặc use reflection:
```php
/**
 * Reconstruct User aggregate from database (without triggering events).
 * Used when loading from persistence.
 */
public static function fromPersistence(
    UserId $id,
    FullName $fullName,
    Email $email,
    ?StudentCode $studentCode = null,
    PhoneNumber $phoneNumber = null,
    ?FacultyId $facultyId = null,
    ?DepartmentId $departmentId = null
): self {
    // Create without triggering events
    $user = new self(
        $id,
        $fullName,
        $email,
        $studentCode,
        $phoneNumber,
        $facultyId,
        $departmentId
    );
    
    // Don't record events - this is reconstruction from persistence
    return $user;
}
```

**Verification**:
- [ ] Repository implemented
- [ ] Integration tests pass
- [ ] Coverage >= 90%

---

#### Subtask 2.3.1.2: EloquentFacultyRepository và EloquentDepartmentRepository

**Estimated Time**: 4 giờ mỗi cái

**Steps**:
1. [ ] Implement EloquentFacultyRepository
2. [ ] Implement EloquentDepartmentRepository
3. [ ] Write integration tests cho cả hai

**Verification**:
- [ ] Both repositories implemented
- [ ] Tests pass

---

### Task 2.3.2: Refactor Controllers

**Estimated Time**: 1.5 ngày (12 giờ)

**Mục tiêu**: Refactor existing Controllers để sử dụng Use Cases

#### Subtask 2.3.2.1: Refactor UserController

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Review existing UserController:
   ```bash
   # Read app/Http/Controllers/Admin/UserController.php
   ```
2. [ ] Tạo new UserController trong Infrastructure:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Http/Controllers
   touch app/OrganizationalStructure/Infrastructure/Http/Controllers/UserController.php
   ```
3. [ ] Implement controller sử dụng Use Cases:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;
   
   use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
   use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
   use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
   use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
   use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use Illuminate\Http\Request;
   use Illuminate\Http\JsonResponse;
   
   /**
    * User controller.
    */
   final class UserController
   {
       public function __construct(
           private readonly CreateUserUseCase $createUserUseCase,
           private readonly UpdateUserProfileUseCase $updateUserProfileUseCase,
           private readonly FindUserUseCase $findUserUseCase
       ) {
       }
       
       public function store(Request $request): JsonResponse
       {
           $dto = new CreateUserDTO(
               firstName: $request->input('first_name'),
               lastName: $request->input('last_name'),
               email: $request->input('email'),
               studentCode: $request->input('code'),
               phone: $request->input('phone'),
               facultyId: $request->input('faculty_id'),
               departmentId: $request->input('department_id')
           );
           
           $user = $this->createUserUseCase->execute($dto);
           
           return response()->json([
               'id' => $user->id()->toString(),
               'email' => $user->email()->toString(),
               'full_name' => $user->fullName()->fullName(),
           ], 201);
       }
       
       // Add other methods...
   }
   ```
4. [ ] Update routes để sử dụng new controller
5. [ ] Write feature tests cho controller

**Code Review Checklist**:
- [ ] ✅ Controller là `final`
- [ ] ✅ Dependencies injected via constructor
- [ ] ✅ Controller chỉ làm HTTP concerns (validation, response)
- [ ] ✅ Business logic trong Use Cases
- [ ] ✅ Proper error handling
- [ ] ✅ Proper HTTP status codes

**Verification**:
- [ ] Controller refactored
- [ ] Routes updated
- [ ] Feature tests pass

---

#### Subtask 2.3.2.2: Refactor FacultyController và DepartmentController

**Estimated Time**: 4 giờ mỗi cái

**Steps**:
1. [ ] Refactor FacultyController
2. [ ] Refactor DepartmentController
3. [ ] Write feature tests

**Verification**:
- [ ] Both controllers refactored
- [ ] Tests pass

---

### Task 2.3.3: Refactor Livewire Components

**Estimated Time**: 1.5 ngày (12 giờ)

**Mục tiêu**: Refactor Livewire components để sử dụng Use Cases

#### Subtask 2.3.3.1: Refactor User Livewire Components

**Estimated Time**: 6 giờ

**Steps**:
1. [ ] Review existing Livewire components:
   ```bash
   # Review app/Livewire/User/*.php
   ```
2. [ ] Tạo new Livewire components trong Infrastructure:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Http/Livewire/User
   ```
3. [ ] Refactor components để sử dụng Use Cases
4. [ ] Update views nếu cần
5. [ ] Write feature tests

**Verification**:
- [ ] Components refactored
- [ ] Tests pass

---

#### Subtask 2.3.3.2: Refactor Faculty và Department Livewire Components

**Estimated Time**: 6 giờ

**Steps**:
1. [ ] Refactor Faculty Livewire components
2. [ ] Refactor Department Livewire components
3. [ ] Write feature tests

**Verification**:
- [ ] Components refactored
- [ ] Tests pass

---

### Task 2.3.4: Tạo Service Provider

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo Service Provider để bind Repository Interfaces

**Steps**:
1. [ ] Tạo Service Provider:
   ```bash
   mkdir -p app/OrganizationalStructure/Infrastructure/Providers
   touch app/OrganizationalStructure/Infrastructure/Providers/OrganizationalStructureServiceProvider.php
   ```
2. [ ] Implement Service Provider:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Infrastructure\Providers;
   
   use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
   use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
   use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
   use App\OrganizationalStructure\Infrastructure\Persistence\EloquentUserRepository;
   use App\OrganizationalStructure\Infrastructure\Persistence\EloquentFacultyRepository;
   use App\OrganizationalStructure\Infrastructure\Persistence\EloquentDepartmentRepository;
   use Illuminate\Support\ServiceProvider;
   
   /**
    * Service provider for OrganizationalStructure context.
    */
   final class OrganizationalStructureServiceProvider extends ServiceProvider
   {
       public function register(): void
       {
           $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
           $this->app->bind(FacultyRepositoryInterface::class, EloquentFacultyRepository::class);
           $this->app->bind(DepartmentRepositoryInterface::class, EloquentDepartmentRepository::class);
       }
   }
   ```
3. [ ] Register Service Provider trong `config/app.php`:
   ```php
   'providers' => [
       // ...
       App\OrganizationalStructure\Infrastructure\Providers\OrganizationalStructureServiceProvider::class,
   ],
   ```

**Verification**:
- [ ] Service Provider created
- [ ] Registered in config
- [ ] Bindings work correctly

---

### Task 2.3.5: Commit Infrastructure Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all tests
3. [ ] Check coverage
4. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Infrastructure/
   git add config/app.php
   git add tests/
   git commit -m "feat(OrganizationalStructure): add Infrastructure Layer

   - Implement Eloquent repositories for User, Faculty, Department
   - Refactor Controllers to use Use Cases
   - Refactor Livewire components to use Use Cases
   - Add OrganizationalStructureServiceProvider
   - Add comprehensive integration tests with 90%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 90%
- [ ] Commit successful

---

## Task 2.4: Data Migration Strategy

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Plan và implement data migration từ old structure sang new DDD structure

### Task 2.4.1: Plan Data Migration và UUID Population

**Estimated Time**: 2 giờ

**⚠️ UUID Migration**: Task này bao gồm populate UUIDs cho existing records theo UUID Migration Strategy.

**Steps**:
1. [ ] Review UUID Migration Strategy:
   - [ ] Đọc `.ai-knowledge/migration-strategy/uuid-migration-strategy.md`
   - [ ] Hiểu rõ approach: populate UUIDs cho existing records
   - [ ] Review populate UUIDs command structure
2. [ ] Review existing data:
   - [ ] Users data structure (count, relationships)
   - [ ] Faculties data structure
   - [ ] Departments data structure
   - [ ] Relationships và foreign keys
3. [ ] Identify data mapping:
   - [ ] Old User model → New User Aggregate
   - [ ] Old Faculty model → New Faculty Entity
   - [ ] Old Department model → New Department Entity
   - [ ] **UUIDs cho existing records** (new requirement)
4. [ ] Plan migration strategy:
   - [ ] Populate UUIDs cho existing records
   - [ ] Populate foreign key UUIDs
   - [ ] Migrate data sang DDD structure
   - [ ] Validation strategy
   - [ ] Rollback strategy
5. [ ] Document migration plan

**Verification**:
- [ ] UUID Migration Strategy reviewed
- [ ] Data reviewed
- [ ] Mapping identified
- [ ] Plan documented

---

### Task 2.4.2: Populate UUIDs cho Existing Records

**Estimated Time**: 2 giờ

**Mục tiêu**: Populate UUID columns cho existing records trong OrganizationalStructure tables

**Steps**:
1. [ ] Create populate UUIDs command:
   ```bash
   php artisan make:command PopulateOrganizationalStructureUuids
   ```
2. [ ] Implement populate logic (xem code example trong UUID Migration Strategy document)
3. [ ] Test populate command với test data
4. [ ] Run populate command trên staging
5. [ ] Verify UUIDs được populate đúng

**Verification**:
- [ ] Populate UUIDs command created
- [ ] UUIDs populated cho existing records
- [ ] Foreign key UUIDs populated
- [ ] Command tested và verified

---

### Task 2.4.3: Make UUID Required và Unique

**Estimated Time**: 30 phút

**Mục tiêu**: Make UUID columns required và unique sau khi đã populate

**Steps**:
1. [ ] Create migration
2. [ ] Make UUID columns `not null` và `unique`
3. [ ] Test migration
4. [ ] Commit

**Verification**:
- [ ] UUID columns made required và unique
- [ ] Migration tested
- [ ] Integer ID columns vẫn giữ nguyên

---

### Task 2.4.4: Create Data Migration Scripts

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Create migration command:
   ```bash
   php artisan make:command MigrateToDDDStructure
   ```
2. [ ] Implement migration logic:
   ```php
   public function handle(): int
   {
       $this->info('Starting data migration...');
       
       // Migrate users (sử dụng UUID từ database, không generate mới)
       $this->migrateUsers();
       
       // Migrate faculties
       $this->migrateFaculties();
       
       // Migrate departments
       $this->migrateDepartments();
       
       $this->info('Data migration completed!');
       
       return self::SUCCESS;
   }
   ```
3. [ ] **Important**: Sử dụng UUID từ database (không generate mới):
   - [ ] Read UUID từ existing records
   - [ ] Use UUID khi tạo Domain aggregates
   - [ ] Ensure UUID consistency
4. [ ] Add validation:
   - [ ] Validate data before migration
   - [ ] Validate data after migration
   - [ ] Report errors
5. [ ] Add rollback capability
6. [ ] Test migration script với test data
7. [ ] Commit

**Verification**:
- [ ] Migration script created
- [ ] UUIDs được sử dụng từ database (không generate mới)
- [ ] Tested với test data
- [ ] Committed

---

### Task 2.4.3: Execute và Validate Data Migration

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Backup database trước khi migrate
2. [ ] Run migration script:
   ```bash
   php artisan migrate:to-ddd-structure
   ```
3. [ ] Validate migrated data:
   - [ ] Check data integrity
   - [ ] Check relationships
   - [ ] Check data counts
4. [ ] Fix any issues
5. [ ] Document results

**Verification**:
- [ ] Migration executed
- [ ] Data validated
- [ ] No data loss
- [ ] Results documented

---

## Task 2.5: Migrate Import Students Feature

**Estimated Time**: 2-3 ngày (16-24 giờ)

**Mục tiêu**: Migrate Import Students Feature sang DDD architecture

### Task 2.5.1: Tạo ImportUsersFromExcelUseCase

**Estimated Time**: 1 ngày (8 giờ)

#### Subtask 2.4.1.1: Review Existing Import Logic

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Review `app/Imports/StudentsImport.php`
2. [ ] Review `app/Jobs/ImportStudentsJob.php`
3. [ ] Review `app/Livewire/Faculty/ImportStudents.php`
4. [ ] Identify business logic cần migrate
5. [ ] Document requirements

**Verification**:
- [ ] Existing code reviewed
- [ ] Requirements documented

---

#### Subtask 2.4.1.2: Implement ImportUsersFromExcelUseCase

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Tạo Use Case:
   ```bash
   touch app/OrganizationalStructure/Application/UseCases/ImportUsersFromExcelUseCase.php
   ```
2. [ ] Implement Use Case:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\OrganizationalStructure\Application\UseCases;
   
   use App\OrganizationalStructure\Application\DTOs\ImportUsersDTO;
   use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
   use App\OrganizationalStructure\Domain\ValueObjects\UserId;
   use App\OrganizationalStructure\Domain\ValueObjects\FullName;
   use App\OrganizationalStructure\Domain\ValueObjects\StudentCode;
   use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
   use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\OrganizationalStructure\Domain\Aggregates\User;
   use App\OrganizationalStructure\Domain\Events\UsersWereImported;
   use App\SharedKernel\Infrastructure\EventDispatcher\EventDispatcherInterface;
   use Illuminate\Support\Collection;
   use Illuminate\Support\Facades\DB;
   
   /**
    * Use case for importing users from Excel file.
    */
   final class ImportUsersFromExcelUseCase
   {
       public function __construct(
           private readonly UserRepositoryInterface $userRepository,
           private readonly EventDispatcherInterface $eventDispatcher
       ) {
       }
       
       /**
        * Import users from Excel collection.
        *
        * @return array{imported: int, errors: int, errors_detail: array}
        */
       public function execute(Collection $rows, int $facultyId): array
       {
           return DB::transaction(function () use ($rows, $facultyId) {
               $imported = 0;
               $errors = 0;
               $errorsDetail = [];
               
               // Pre-fetch existing users để optimize
               $emails = $rows->pluck('email')->filter()->unique()->toArray();
               $codes = $rows->pluck('ma_sinh_vien')->filter()->unique()->toArray();
               
               $existingUsers = collect();
               // Note: Cần implement findByEmails và findByStudentCodes trong repository
               
               foreach ($rows as $row) {
                   try {
                       $email = Email::fromString($row['email']);
                       $fullName = FullName::fromParts($row['ten'], $row['ho']);
                       $studentCode = StudentCode::fromString($row['ma_sinh_vien']);
                       $phone = isset($row['so_dien_thoai']) ? PhoneNumber::fromString($row['so_dien_thoai']) : PhoneNumber::empty();
                       $facultyIdVO = FacultyId::fromInt($facultyId);
                       
                       // Check if user exists
                       $existingUser = $this->userRepository->findByStudentCode($studentCode);
                       
                       if ($existingUser !== null) {
                           // Update existing user
                           $existingUser->updateProfile($fullName, $email, $phone);
                           $existingUser->assignToFaculty($facultyIdVO);
                           $this->userRepository->save($existingUser);
                       } else {
                           // Create new user
                           $userId = UserId::generate();
                           $user = User::create(
                               $userId,
                               $fullName,
                               $email,
                               $studentCode,
                               $phone,
                               $facultyIdVO,
                               null
                           );
                           $this->userRepository->save($user);
                       }
                       
                       $imported++;
                   } catch (\Throwable $e) {
                       $errors++;
                       $errorsDetail[] = [
                           'row' => $row->toArray(),
                           'error' => $e->getMessage(),
                       ];
                   }
               }
               
               // Dispatch import completed event
               $this->eventDispatcher->dispatch(new UsersWereImported(
                   $facultyId,
                   $imported,
                   $errors
               ));
               
               return [
                   'imported' => $imported,
                   'errors' => $errors,
                   'errors_detail' => $errorsDetail,
               ];
           });
       }
   }
   ```
3. [ ] Write feature tests:
   - [ ] Test successful import
   - [ ] Test duplicate handling
   - [ ] Test error handling
   - [ ] Test event dispatch

**Verification**:
- [ ] Use Case implemented
- [ ] Feature tests pass

---

#### Subtask 2.4.1.2: Refactor StudentsImport Class

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Refactor `app/Imports/StudentsImport.php` để sử dụng Use Case
2. [ ] Update để call Use Case thay vì direct database access
3. [ ] Write tests

**Verification**:
- [ ] Import class refactored
- [ ] Tests pass

---

#### Subtask 2.4.1.3: Refactor ImportStudentsJob

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Refactor `app/Jobs/ImportStudentsJob.php` để sử dụng Use Case
2. [ ] Update error handling
3. [ ] Write tests

**Verification**:
- [ ] Job refactored
- [ ] Tests pass

---

### Task 2.4.2: Refactor ImportStudents Livewire Component

**Estimated Time**: 0.5 ngày (4 giờ)

**Steps**:
1. [ ] Refactor `app/Livewire/Faculty/ImportStudents.php` để sử dụng Use Case
2. [ ] Update để call Use Case
3. [ ] Maintain existing UI/UX
4. [ ] Write feature tests

**Verification**:
- [ ] Component refactored
- [ ] UI/UX maintained
- [ ] Tests pass

---

### Task 2.5.3: Commit Import Feature Migration

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all tests
3. [ ] Manual testing:
   - [ ] Test import với file Excel hợp lệ
   - [ ] Test import với duplicate users
   - [ ] Test import với invalid data
   - [ ] Test progress updates
   - [ ] Test completion notification
4. [ ] Commit:
   ```bash
   git add app/OrganizationalStructure/Application/UseCases/ImportUsersFromExcelUseCase.php
   git add app/Imports/StudentsImport.php
   git add app/Jobs/ImportStudentsJob.php
   git add app/Livewire/Faculty/ImportStudents.php
   git commit -m "feat(OrganizationalStructure): migrate Import Students feature to DDD

   - Add ImportUsersFromExcelUseCase
   - Refactor StudentsImport to use Use Case
   - Refactor ImportStudentsJob to use Use Case
   - Refactor ImportStudents Livewire component
   - Maintain backward compatibility
   - Add comprehensive tests"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Manual testing successful
- [ ] Commit successful

---

## Milestones và Progress Tracking

### Milestone 2.1: Domain Layer Complete

**Estimated Time**: 3-4 ngày

**Progress Checklist**:
- [ ] ✅ Value Objects created và tested
- [ ] ✅ User Aggregate created và tested
- [ ] ✅ Faculty và Department Entities created và tested
- [ ] ✅ Domain Events created
- [ ] ✅ Repository Interfaces created
- [ ] ✅ Domain Exceptions created
- [ ] ✅ Unit tests coverage >= 90%

**Status**: ⏳ In Progress

---

### Milestone 2.2: Application Layer Complete

**Estimated Time**: 4-5 ngày

**Progress Checklist**:
- [ ] ✅ DTOs created
- [ ] ✅ All Use Cases implemented và tested
- [ ] ✅ Transaction và outbox pattern implemented
- [ ] ✅ Feature tests coverage >= 90%

**Status**: ⏳ Pending

---

### Milestone 2.3: Infrastructure Layer Complete

**Estimated Time**: 5-6 ngày

**Progress Checklist**:
- [ ] ✅ Repository implementations created và tested
- [ ] ✅ Controllers refactored
- [ ] ✅ Livewire components refactored
- [ ] ✅ Service Provider created và registered
- [ ] ✅ Integration tests coverage >= 90%

**Status**: ⏳ Pending

---

### Milestone 2.4: Data Migration Complete

**Estimated Time**: 1 ngày

**Progress Checklist**:
- [ ] ✅ Data migration plan created
- [ ] ✅ Data migration scripts created và tested
- [ ] ✅ Data migration executed và validated
- [ ] ✅ No data loss
- [ ] ✅ Results documented

**Status**: ⏳ Pending

---

### Milestone 2.5: Import Feature Migrated

**Estimated Time**: 2-3 ngày

**Progress Checklist**:
- [ ] ✅ ImportUsersFromExcelUseCase created
- [ ] ✅ StudentsImport refactored
- [ ] ✅ ImportStudentsJob refactored
- [ ] ✅ ImportStudents Livewire component refactored
- [ ] ✅ Manual testing successful

**Status**: ⏳ Pending

---

## Quick Reference Commands

### Development Commands

```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Unit/OrganizationalStructure/Domain/ValueObjects/FullNameTest.php

# Run with coverage
php artisan test --coverage --min=90

# Code formatting
./vendor/bin/pint app/OrganizationalStructure/

# PHPStan (if configured)
./vendor/bin/phpstan analyse app/OrganizationalStructure/
```

### Git Commands

```bash
# Create feature branch
git checkout -b feat/migrate-ddd-phase2

# Stage files
git add app/OrganizationalStructure/

# Commit
git commit -m "feat(OrganizationalStructure): <description>"

# Push
git push origin feat/migrate-ddd-phase2
```

### Verification Commands

```bash
# Syntax check
php -l app/OrganizationalStructure/Domain/Aggregates/User.php

# Test coverage
php artisan test --coverage

# Check database
php artisan db:show
```

---

## Troubleshooting Guide

### Issue: Repository không tìm thấy User từ database

**Symptoms**: `findById()` returns null mặc dù user tồn tại trong database

**Root Cause**: ID mismatch giữa database (int) và Domain (UUID string)

**Solution**:
1. Check database schema - users table có `id` là int hay UUID?
2. Nếu là int, cần update UserId Value Object để support cả int và UUID
3. Hoặc migrate database để sử dụng UUID

**Prevention**: Review database schema trước khi implement repository

---

### Issue: Domain Events không được dispatch

**Symptoms**: Events được record nhưng không được dispatch

**Root Cause**: EventDispatcher không được inject đúng cách

**Solution**:
1. Check Service Provider bindings
2. Verify EventDispatcherInterface binding trong SharedKernelServiceProvider
3. Check Use Case constructor injection

**Prevention**: Write integration tests để verify event dispatch

---

### Issue: Livewire component không update sau khi refactor

**Symptoms**: UI không reflect changes sau khi migrate

**Root Cause**: Component không được update đúng cách hoặc routes chưa được update

**Solution**:
1. Check routes file
2. Verify Livewire component namespace
3. Check view files có được update không
4. Clear cache: `php artisan optimize:clear`

**Prevention**: Write feature tests cho Livewire components

---

### Issue: Import feature không hoạt động sau migration

**Symptoms**: Import fails hoặc không import users

**Root Cause**: Use Case logic không đúng hoặc repository methods missing

**Solution**:
1. Check ImportUsersFromExcelUseCase logic
2. Verify repository có methods: `findByEmails()`, `findByStudentCodes()` nếu cần
3. Check error logs
4. Test Use Case independently

**Prevention**: Write comprehensive tests cho import feature

---

## Common Questions & Answers

### Q: Có cần migrate tất cả Controllers và Livewire components ngay không?

**A**: Không nhất thiết. Có thể migrate từng phần:
1. Migrate Controllers trước
2. Migrate Livewire components sau
3. Hoặc migrate theo feature (User management trước, Faculty/Department sau)

**Recommendation**: Migrate theo thứ tự ưu tiên business value.

---

### Q: Làm sao đảm bảo backward compatibility?

**A**: 
1. Giữ old Controllers/Livewire components trong thời gian transition
2. Update routes để point đến new controllers
3. Test thoroughly trước khi remove old code
4. Có rollback plan

**Recommendation**: Maintain both versions trong 1-2 sprints trước khi remove old code.

---

### Q: Test coverage bao nhiêu là đủ?

**A**: 
- Domain Layer: >= 95% (critical)
- Application Layer: >= 90%
- Infrastructure Layer: >= 85%

**Recommendation**: Focus vào quality hơn là số lượng. Test critical paths và edge cases.

---

### Q: Có cần migrate tất cả features trong Phase 2 không?

**A**: Không nhất thiết. Có thể:
1. Migrate core features trước (User, Faculty, Department CRUD)
2. Migrate advanced features sau (Import, Search, Filters)

**Recommendation**: Migrate theo business priority và complexity.

---

## Deployment Strategy

**⚠️ CRITICAL**: Phase 2 là phase lớn với nhiều changes. Cần deployment strategy cẩn thận để đảm bảo không break production.

### Pre-Deployment Checklist

**Trước khi deploy Phase 2, đảm bảo**:

- [ ] ✅ **All tests pass** (100% pass rate)
- [ ] ✅ **Test coverage >= 90%** cho tất cả layers
- [ ] ✅ **Code review completed** và approved
- [ ] ✅ **Security review passed** (nếu có security implications)
- [ ] ✅ **Database backup created**
- [ ] ✅ **Staging environment tested** thoroughly
- [ ] ✅ **Feature flags configured** (nếu sử dụng)
- [ ] ✅ **Rollback plan ready**
- [ ] ✅ **Monitoring setup** và tested
- [ ] ✅ **Team notified** về deployment
- [ ] ✅ **Maintenance window scheduled** (nếu cần)

---

### Deployment Steps

#### Step 1: Pre-Deployment (30 phút)

1. [ ] **Create deployment branch**:
   ```bash
   git checkout -b release/phase2-v1.0
   git push origin release/phase2-v1.0
   ```

2. [ ] **Final verification**:
   ```bash
   # Run all tests
   php artisan test
   
   # Check code quality
   ./vendor/bin/pint --test
   ./vendor/bin/phpstan analyse
   
   # Check coverage
   php artisan test --coverage --min=90
   ```

3. [ ] **Database backup**:
   ```bash
   php artisan db:backup
   # or
   mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

4. [ ] **Environment check**:
   - [ ] Staging environment ready
   - [ ] Production environment ready
   - [ ] Database migrations tested
   - [ ] Config files updated

---

#### Step 2: Staging Deployment (1 giờ)

1. [ ] **Deploy to staging**:
   ```bash
   # Deploy code
   git checkout release/phase2-v1.0
   git pull origin release/phase2-v1.0
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   
   # Run migrations
   php artisan migrate --force
   
   # Clear cache
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. [ ] **Verify staging deployment**:
   - [ ] Application starts successfully
   - [ ] Database connections work
   - [ ] Routes accessible
   - [ ] No errors in logs

3. [ ] **Staging testing**:
   - [ ] Test user creation flow
   - [ ] Test faculty/department management
   - [ ] Test import feature
   - [ ] Test API endpoints
   - [ ] Test Livewire components
   - [ ] Performance check

4. [ ] **Fix any issues** found trong staging

---

#### Step 3: Production Deployment (1 giờ)

**Option A: Feature Flags (Recommended)**

1. [ ] **Enable feature flags**:
   ```php
   // config/features.php
   'organizational_structure_ddd' => env('FEATURE_DDD_ORG_STRUCTURE', false),
   ```

2. [ ] **Deploy code** (features OFF):
   ```bash
   # Deploy code với feature flags OFF
   git checkout release/phase2-v1.0
   git pull origin release/phase2-v1.0
   
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. [ ] **Verify production** (features OFF):
   - [ ] Application works với old code
   - [ ] No errors
   - [ ] Performance OK

4. [ ] **Enable feature flags gradually**:
   ```bash
   # Enable cho một phần users trước (nếu có A/B testing)
   # Hoặc enable cho internal users
   # Hoặc enable fully
   ```

5. [ ] **Monitor**:
   - [ ] Check error logs
   - [ ] Check performance metrics
   - [ ] Check user feedback

6. [ ] **Enable fully** nếu không có issues:
   ```bash
   # Update .env
   FEATURE_DDD_ORG_STRUCTURE=true
   php artisan config:cache
   ```

**Option B: Blue-Green Deployment (Nếu có infrastructure)**

1. [ ] **Deploy to green environment**
2. [ ] **Test green environment**
3. [ ] **Switch traffic** từ blue sang green
4. [ ] **Monitor** green environment
5. [ ] **Keep blue** as backup

**Option C: Direct Deployment (Không recommended, chỉ nếu không có feature flags)**

1. [ ] **Schedule maintenance window**
2. [ ] **Deploy code**
3. [ ] **Run migrations**
4. [ ] **Verify**
5. [ ] **End maintenance window**

---

#### Step 4: Post-Deployment Verification (30 phút)

1. [ ] **Smoke tests**:
   ```bash
   # Test critical paths
   - User login
   - User creation
   - Faculty listing
   - API endpoints
   ```

2. [ ] **Check logs**:
   ```bash
   tail -f storage/logs/laravel.log
   # Check for errors
   ```

3. [ ] **Check monitoring**:
   - [ ] Error rate (should be 0 or very low)
   - [ ] Response time (should be acceptable)
   - [ ] Database queries (should be optimized)
   - [ ] Memory usage (should be normal)

4. [ ] **Check database**:
   ```bash
   # Verify data integrity
   php artisan tinker
   # Check user counts, relationships, etc.
   ```

5. [ ] **User acceptance**:
   - [ ] Test với real users (internal)
   - [ ] Collect feedback
   - [ ] Monitor for issues

---

### Rollback Procedures

**Nếu có issues sau deployment**:

#### Quick Rollback (Feature Flags)

1. [ ] **Disable feature flags**:
   ```bash
   # Update .env
   FEATURE_DDD_ORG_STRUCTURE=false
   php artisan config:cache
   ```
2. [ ] **Verify** old code works
3. [ ] **Investigate** issues
4. [ ] **Fix** và redeploy

#### Full Rollback (Code)

1. [ ] **Stop application** (nếu critical):
   ```bash
   # Put application in maintenance mode
   php artisan down
   ```

2. [ ] **Revert code**:
   ```bash
   git checkout <previous-stable-version>
   git pull origin <previous-stable-version>
   ```

3. [ ] **Revert migrations** (nếu cần):
   ```bash
   php artisan migrate:rollback --step=X
   ```

4. [ ] **Restore database** (nếu cần):
   ```bash
   mysql -u user -p database < backup_YYYYMMDD_HHMMSS.sql
   ```

5. [ ] **Restart application**:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan up
   ```

6. [ ] **Verify** application works

7. [ ] **Document** rollback reason và issues

---

### Feature Flags Strategy

**Implementation**:

1. [ ] **Install feature flags package** (nếu chưa có):
   ```bash
   composer require laravel/feature
   ```

2. [ ] **Configure feature flags**:
   ```php
   // config/features.php
   return [
       'organizational_structure_ddd' => [
           'default' => env('FEATURE_DDD_ORG_STRUCTURE', false),
           'description' => 'Enable DDD OrganizationalStructure Context',
       ],
   ];
   ```

3. [ ] **Use trong code**:
   ```php
   use Laravel\Feature\Feature;

   if (Feature::active('organizational_structure_ddd')) {
       // Use new DDD code
       $user = $this->createUserUseCase->execute($dto);
   } else {
       // Use old code
       $user = User::create($data);
   }
   ```

4. [ ] **Gradual rollout**:
   - Week 1: Internal users only
   - Week 2: 25% of users
   - Week 3: 50% of users
   - Week 4: 100% of users

---

### Monitoring Checklist

**After deployment, monitor**:

- [ ] ✅ **Error rate** (should be < 0.1%)
- [ ] ✅ **Response time** (should be < 200ms for most endpoints)
- [ ] ✅ **Database query time** (should be < 50ms)
- [ ] ✅ **Memory usage** (should be stable)
- [ ] ✅ **CPU usage** (should be normal)
- [ ] ✅ **Failed requests** (should be 0 or very low)
- [ ] ✅ **User feedback** (no complaints)
- [ ] ✅ **Security events** (no suspicious activity)

**Monitoring Tools**:
- Laravel Telescope (development)
- Laravel Log (production)
- Application Performance Monitoring (APM) tool
- Database monitoring
- Server monitoring

---

### Deployment Timeline

**Recommended Timeline**:

- **Day 1**: Deploy to staging, test thoroughly
- **Day 2**: Deploy to production với feature flags OFF
- **Day 3**: Enable feature flags cho internal users
- **Day 4-7**: Monitor và gradually enable cho all users
- **Day 8+**: Full rollout nếu no issues

---

## Summary

Phase 2 là một phase lớn và quan trọng, migrate toàn bộ OrganizationalStructure Context sang DDD architecture. Cần:

1. ✅ **Follow workflow**: Planning → Setup → Implementation → Testing → Code Quality → Documentation → Commit
2. ✅ **Small commits**: Commit sau mỗi subtask
3. ✅ **TDD**: Write tests trước khi implement
4. ✅ **Code quality**: Run Pint, PHPStan, check coverage
5. ✅ **Documentation**: Update PHPDoc và comments
6. ✅ **Testing**: Unit tests, Feature tests, Integration tests, Manual testing
7. ✅ **Backward compatibility**: Đảm bảo không break existing functionality
8. ✅ **Deployment strategy**: Deploy safely với feature flags và rollback plan

**Estimated Total Time**: 15-19 ngày làm việc (120-152 giờ)

**Success Criteria**:
- ✅ Domain Layer hoàn chỉnh với test coverage >= 90%
- ✅ Application Layer hoàn chỉnh với test coverage >= 90%
- ✅ Infrastructure Layer hoàn chỉnh với test coverage >= 90%
- ✅ Data migration completed và validated
- ✅ Import feature migrated và tested
- ✅ Backward compatibility maintained
- ✅ Code quality standards met
- ✅ Documentation complete
- ✅ Production deployment successful
