# Phase 1: Shared Kernel và Cơ sở hạ tầng - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## Tổng quan

Phase 1 là nền tảng quan trọng nhất của toàn bộ migration. Đây là phase mà chúng ta xây dựng các thành phần dùng chung (Shared Kernel) và infrastructure cơ bản mà tất cả các Bounded Contexts sẽ phụ thuộc vào. **Lỗi ở phase này sẽ ảnh hưởng đến toàn bộ project**, vì vậy cần thận trọng và làm đúng ngay từ đầu.

### Mục tiêu Phase 1

1. ✅ Tạo Shared Kernel với các Value Objects, Exceptions, và Interfaces cơ bản
2. ✅ Implement Outbox Pattern để đảm bảo tính nhất quán dữ liệu khi giao tiếp giữa các Bounded Contexts
3. ✅ Setup infrastructure cần thiết cho các phases tiếp theo
4. ✅ Đảm bảo code quality cao với test coverage >= 90%
5. ✅ Tạo documentation đầy đủ cho team

### Thời gian ước tính

**Tổng thời gian**: 4-6 ngày làm việc (32-48 giờ)

**Phân bổ**:
- Task 1.1: Shared Kernel Domain Layer (1.5-2 ngày)
- Task 1.2: Shared Kernel Infrastructure (0.5-1 ngày)
- Task 1.3: Outbox Pattern (1-1.5 ngày)
- Task 1.4: Monitoring & Observability Setup (0.5 ngày)
- Task 1.5: Cross-Context Communication Strategy (1 giờ)
- Task 1.6: Testing & Documentation (0.5-1 ngày)

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 1, đảm bảo:

### Setup Checklist

- [ ] ✅ Đã review và approve architecture design trong `.ai-knowledge/commons/02-architecture.md`
- [ ] ✅ Đã setup development environment (PHP 8.3, Laravel 12, Composer)
- [ ] ✅ Đã tạo feature branch: `feat/migrate-ddd-phase1`
- [ ] ✅ Đã install các dependencies cần thiết:
  ```bash
  composer require ramsey/uuid
  composer require --dev phpunit/phpunit
  ```
- [ ] ✅ Đã đọc và hiểu các conventions trong `.ai-knowledge/commons/03-conventions.md`
- [ ] ✅ Đã review code examples trong `.ai-knowledge/commons/04-code-examples.md`
- [ ] ✅ Đã setup CI/CD để chạy tests tự động
- [ ] ✅ Đã có database backup (nếu cần)

### Environment Verification

**Steps để verify environment**:
1. [ ] Check PHP version:
   ```bash
   php -v  # Should be PHP 8.3.x
   ```
2. [ ] Check Laravel version:
   ```bash
   php artisan --version  # Should be Laravel 12.x
   ```
3. [ ] Check Composer:
   ```bash
   composer --version
   ```
4. [ ] Check Git branch:
   ```bash
   git branch  # Should be on feat/migrate-ddd-phase1
   ```
5. [ ] Check dependencies:
   ```bash
   composer show ramsey/uuid
   composer show phpunit/phpunit
   ```
6. [ ] Run existing tests để đảm bảo environment OK:
   ```bash
   php artisan test
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ Environment ready for development

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (5-10 phút)
   - [ ] Đọc task description và requirements
   - [ ] Review code examples từ `.ai-knowledge/commons/04-code-examples.md`
   - [ ] Xác định dependencies và prerequisites
   - [ ] Estimate time

2. **Setup** (10-15 phút)
   - [ ] Tạo files và folders cần thiết
   - [ ] Verify prerequisites
   - [ ] Setup test files

3. **Implementation** (varies)
   - [ ] Implement code theo requirements
   - [ ] Follow code conventions
   - [ ] Add PHPDoc comments
   - [ ] Verify syntax

4. **Testing** (varies)
   - [ ] Write unit tests
   - [ ] Write integration tests (nếu cần)
   - [ ] Run tests và verify pass
   - [ ] Check coverage >= 90%

5. **Code Quality** (15-20 phút)
   - [ ] Run Pint để format code
   - [ ] Run PHPStan (nếu có)
   - [ ] Fix linting issues
   - [ ] Review code một lần nữa

6. **Manual Testing** (15-20 phút)
   - [ ] Test manually với test script
   - [ ] Verify behavior đúng như mong đợi
   - [ ] Test edge cases

7. **Commit & Review** (20-30 phút)
   - [ ] Stage files
   - [ ] Write meaningful commit message
   - [ ] Commit và push
   - [ ] Create PR (nếu cần)
   - [ ] Request code review

8. **Verification** (10 phút)
   - [ ] Verify CI/CD passes
   - [ ] Address review comments (nếu có)
   - [ ] Mark task as completed

### Best Practices

- ✅ **Small commits**: Commit thường xuyên với small, focused changes
- ✅ **Meaningful messages**: Commit messages rõ ràng và mô tả đúng changes
- ✅ **Test first**: Viết tests trước khi implement khi có thể (TDD)
- ✅ **Code review**: Luôn request code review trước khi merge
- ✅ **Documentation**: Update documentation ngay khi có thay đổi
- ✅ **Communication**: Communicate với team nếu có blockers

---

## Task 1.1: Shared Kernel Domain Layer

### Mục tiêu

Tạo các thành phần Domain Layer của Shared Kernel bao gồm:
- Value Objects: Email, Uuid, Timestamp
- Exceptions: DomainException, EntityNotFoundException, InvalidArgumentException
- Interfaces: EventDispatcherInterface, ClockInterface

### Cấu trúc thư mục

```
app/SharedKernel/
└── Domain/
    ├── ValueObjects/
    │   ├── Email.php
    │   ├── Uuid.php
    │   └── Timestamp.php
    ├── Exceptions/
    │   ├── DomainException.php
    │   ├── EntityNotFoundException.php
    │   └── InvalidArgumentException.php
    └── Interfaces/
        ├── EventDispatcherInterface.php
        └── ClockInterface.php
```

---

### Task 1.1.1: Email Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Email.php`  
**Estimated Time**: 2-3 giờ  
**Priority**: High (được sử dụng rộng rãi)

#### Subtask 1.1.1.1: Setup và Tạo File

**Estimated Time**: 15 phút

**Steps**:
1. [ ] Tạo thư mục nếu chưa có:
   ```bash
   mkdir -p app/SharedKernel/Domain/ValueObjects
   ```
2. [ ] Tạo file Email.php:
   ```bash
   touch app/SharedKernel/Domain/ValueObjects/Email.php
   ```
3. [ ] Verify file đã được tạo:
   ```bash
   ls -la app/SharedKernel/Domain/ValueObjects/Email.php
   ```

**Verification**:
- [ ] File tồn tại và có quyền đọc/ghi
- [ ] Thư mục structure đúng

---

#### Subtask 1.1.1.2: Implement Email Class

**Estimated Time**: 45-60 phút

**Steps**:
1. [ ] Copy code từ `.ai-knowledge/commons/04-code-examples.md` section 4.1.1
2. [ ] Verify `declare(strict_types=1);` ở dòng đầu tiên
3. [ ] Verify namespace: `namespace App\SharedKernel\Domain\ValueObjects;`
4. [ ] Verify class là `final class Email`
5. [ ] Verify implements `Stringable` interface
6. [ ] Verify constructor là `private`
7. [ ] Verify có static factory method `fromString()`
8. [ ] Verify có private method `validate()`
9. [ ] Verify có method `equals()`
10. [ ] Verify có methods: `domain()`, `localPart()`
11. [ ] Verify có `__toString()` method

**Code Review Checklist**:
- [ ] ✅ Class là `final` để prevent inheritance
- [ ] ✅ Constructor là `private` để enforce immutability
- [ ] ✅ Có static factory method `fromString()`
- [ ] ✅ Validation logic trong private method `validate()`
- [ ] ✅ Implement `Stringable` interface
- [ ] ✅ Có method `equals()` để so sánh
- [ ] ✅ Email được normalize (lowercase, trim)
- [ ] ✅ Exception messages rõ ràng và có ý nghĩa
- [ ] ✅ Tất cả methods có PHPDoc comments
- [ ] ✅ Type hints đầy đủ cho parameters và return types

**Verification**:
- [ ] Syntax check: `php -l app/SharedKernel/Domain/ValueObjects/Email.php`
- [ ] IDE không có errors/warnings
- [ ] Code formatting: `./vendor/bin/pint app/SharedKernel/Domain/ValueObjects/Email.php`

---

#### Subtask 1.1.1.3: Write Unit Tests

**Estimated Time**: 60-90 phút

**Steps**:
1. [ ] Tạo test file:
   ```bash
   mkdir -p tests/Unit/SharedKernel/Domain/ValueObjects
   touch tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php
   ```
2. [ ] Copy test code từ section 6 trong task này
3. [ ] Implement test `test_valid_email_can_be_created()`
4. [ ] Implement test `test_email_is_normalized()`
5. [ ] Implement test `test_invalid_email_throws_exception()`
6. [ ] Implement test `test_empty_email_throws_exception()`
7. [ ] Implement test `test_equals_method()`
8. [ ] Implement test `test_domain_method()`
9. [ ] Implement test `test_local_part_method()`
10. [ ] Thêm edge case tests:
    - [ ] Test với email có nhiều ký tự đặc biệt
    - [ ] Test với email có subdomain
    - [ ] Test với email có plus sign (+)
    - [ ] Test với email có dot trong local part

**Test Execution**:
```bash
# Run specific test file
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php

# Run with verbose output
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php --verbose

# Run with coverage
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php --coverage
```

**Test Coverage Requirements**:
- [ ] ✅ Test tất cả public methods
- [ ] ✅ Test edge cases (empty, invalid format, normalization)
- [ ] ✅ Test equals method
- [ ] ✅ Coverage >= 95%
- [ ] ✅ Tất cả tests pass

**Verification**:
- [ ] All tests pass: `php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php`
- [ ] Coverage >= 95%
- [ ] No skipped tests

---

#### Subtask 1.1.1.4: Code Formatting và Linting

**Estimated Time**: 15 phút

**Steps**:
1. [ ] Run Laravel Pint:
   ```bash
   ./vendor/bin/pint app/SharedKernel/Domain/ValueObjects/Email.php
   ```
2. [ ] Run PHPStan (nếu có):
   ```bash
   ./vendor/bin/phpstan analyse app/SharedKernel/Domain/ValueObjects/Email.php
   ```
3. [ ] Fix any formatting issues
4. [ ] Verify code style consistency

**Verification**:
- [ ] Pint không có errors
- [ ] PHPStan không có errors (nếu có)
- [ ] Code style nhất quán

---

#### Subtask 1.1.1.5: Manual Testing

**Estimated Time**: 20 phút

**Steps**:
1. [ ] Tạo test script tạm thời để test manually:
   ```php
   // test_email.php
   require __DIR__ . '/vendor/autoload.php';
   
   use App\SharedKernel\Domain\ValueObjects\Email;
   
   // Test valid email
   $email = Email::fromString('test@example.com');
   echo "Email: " . $email . "\n";
   echo "Domain: " . $email->domain() . "\n";
   echo "Local part: " . $email->localPart() . "\n";
   
   // Test normalization
   $email2 = Email::fromString('  Test@Example.COM  ');
   echo "Normalized: " . $email2 . "\n";
   ```
2. [ ] Run test script:
   ```bash
   php test_email.php
   ```
3. [ ] Verify output đúng như mong đợi
4. [ ] Test exception cases manually
5. [ ] Clean up test script

**Verification**:
- [ ] Manual tests pass
- [ ] Output đúng như mong đợi
- [ ] Exception messages rõ ràng

---

#### Subtask 1.1.1.6: Commit và Code Review

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Stage files:
   ```bash
   git add app/SharedKernel/Domain/ValueObjects/Email.php
   git add tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php
   ```
2. [ ] Commit với message:
   ```bash
   git commit -m "feat(SharedKernel): add Email value object

   - Add Email value object with validation and normalization
   - Add unit tests with 95%+ coverage
   - Implement Stringable interface
   - Add equals(), domain(), and localPart() methods"
   ```
3. [ ] Push to feature branch:
   ```bash
   git push origin feat/migrate-ddd-phase1
   ```
4. [ ] Create Pull Request (nếu cần)
5. [ ] Request code review từ team member

**Code Review Checklist**:
- [ ] ✅ Code follows conventions
- [ ] ✅ Tests are comprehensive
- [ ] ✅ Documentation is clear
- [ ] ✅ No security issues
- [ ] ✅ Performance is acceptable

**Verification**:
- [ ] Commit successful
- [ ] PR created (nếu cần)
- [ ] Code review requested
- [ ] CI/CD pipeline passes

---

#### Summary Checklist cho Task 1.1.1

- [ ] ✅ File Email.php được tạo và implement đầy đủ
- [ ] ✅ Unit tests được viết và pass
- [ ] ✅ Code coverage >= 95%
- [ ] ✅ Code formatting và linting pass
- [ ] ✅ Manual testing completed
- [ ] ✅ Code committed và reviewed
- [ ] ✅ Ready for next task

**Total Estimated Time**: 2-3 giờ
   - [ ] ✅ Class là `final` để prevent inheritance
   - [ ] ✅ Constructor là `private` để enforce immutability
   - [ ] ✅ Có static factory method `fromString()`
   - [ ] ✅ Validation logic trong private method `validate()`
   - [ ] ✅ Implement `Stringable` interface
   - [ ] ✅ Có method `equals()` để so sánh
   - [ ] ✅ Email được normalize (lowercase, trim)
   - [ ] ✅ Exception messages rõ ràng và có ý nghĩa

4. **Best Practices**
   - ✅ **Immutability**: Email object không thể thay đổi sau khi tạo
   - ✅ **Self-validation**: Email tự validate khi khởi tạo
   - ✅ **Normalization**: Tự động lowercase và trim để đảm bảo consistency
   - ✅ **Type safety**: Sử dụng strict types

5. **Potential Pitfalls**
   - ⚠️ **Email validation**: `FILTER_VALIDATE_EMAIL` có thể không đủ strict cho một số use cases. Nếu cần, có thể sử dụng thư viện như `egulias/email-validator`
   - ⚠️ **Performance**: Normalization (lowercase, trim) được thực hiện mỗi lần tạo object. Đây là acceptable trade-off cho immutability
   - ⚠️ **Exception handling**: Đảm bảo exception messages không expose sensitive information

6. **Unit Tests**

   **File**: `tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

   use App\SharedKernel\Domain\ValueObjects\Email;
   use InvalidArgumentException;
   use PHPUnit\Framework\TestCase;

   final class EmailTest extends TestCase
   {
       /**
        * Test that valid email can be created.
        */
       public function test_valid_email_can_be_created(): void
       {
           $email = Email::fromString('test@example.com');

           $this->assertInstanceOf(Email::class, $email);
           $this->assertEquals('test@example.com', (string) $email);
       }

       /**
        * Test that email is normalized (lowercase, trimmed).
        */
       public function test_email_is_normalized(): void
       {
           $email1 = Email::fromString('  Test@Example.COM  ');
           $email2 = Email::fromString('test@example.com');

           $this->assertEquals('test@example.com', (string) $email1);
           $this->assertTrue($email1->equals($email2));
       }

       /**
        * Test that invalid email throws exception.
        */
       public function test_invalid_email_throws_exception(): void
       {
           $this->expectException(InvalidArgumentException::class);
           $this->expectExceptionMessage('Invalid email format');

           Email::fromString('not-an-email');
       }

       /**
        * Test that empty email throws exception.
        */
       public function test_empty_email_throws_exception(): void
       {
           $this->expectException(InvalidArgumentException::class);
           $this->expectExceptionMessage('Email cannot be empty');

           Email::fromString('');
       }

       /**
        * Test equals method.
        */
       public function test_equals_method(): void
       {
           $email1 = Email::fromString('test@example.com');
           $email2 = Email::fromString('test@example.com');
           $email3 = Email::fromString('other@example.com');

           $this->assertTrue($email1->equals($email2));
           $this->assertFalse($email1->equals($email3));
       }

       /**
        * Test domain method.
        */
       public function test_domain_method(): void
       {
           $email = Email::fromString('user@example.com');

           $this->assertEquals('example.com', $email->domain());
       }

       /**
        * Test localPart method.
        */
       public function test_local_part_method(): void
       {
           $email = Email::fromString('user@example.com');

           $this->assertEquals('user', $email->localPart());
       }
   }
   ```

   **Test Coverage Requirements**:
   - ✅ Test tất cả public methods
   - ✅ Test edge cases (empty, invalid format, normalization)
   - ✅ Test equals method
   - ✅ Coverage >= 95%

---

### Task 1.1.2: Uuid Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Uuid.php`  
**Estimated Time**: 3-4 giờ  
**Priority**: High (được sử dụng cho tất cả IDs)

#### Prerequisites

- [ ] ✅ Đã install `ramsey/uuid`: `composer require ramsey/uuid`
- [ ] ✅ Verify package installed: `composer show ramsey/uuid`
- [ ] ✅ Check version compatibility với PHP 8.3

---

#### Subtask 1.1.2.1: Install Dependencies

**Estimated Time**: 10 phút

**Steps**:
1. [ ] Check if ramsey/uuid already installed:
   ```bash
   composer show ramsey/uuid
   ```
2. [ ] Install if not present:
   ```bash
   composer require ramsey/uuid
   ```
3. [ ] Verify installation:
   ```bash
   composer show ramsey/uuid
   ```
4. [ ] Check composer.json updated:
   ```bash
   grep -A 2 "ramsey/uuid" composer.json
   ```

**Verification**:
- [ ] Package installed successfully
- [ ] Version compatible với PHP 8.3
- [ ] composer.json và composer.lock updated

---

#### Subtask 1.1.2.2: Setup và Tạo File

**Estimated Time**: 10 phút

**Steps**:
1. [ ] Verify thư mục tồn tại:
   ```bash
   ls -la app/SharedKernel/Domain/ValueObjects/
   ```
2. [ ] Tạo file Uuid.php:
   ```bash
   touch app/SharedKernel/Domain/ValueObjects/Uuid.php
   ```
3. [ ] Verify file created:
   ```bash
   ls -la app/SharedKernel/Domain/ValueObjects/Uuid.php
   ```

**Verification**:
- [ ] File tồn tại và có quyền đọc/ghi

---

#### Subtask 1.1.2.3: Implement Uuid Class

**Estimated Time**: 60-90 phút

**Steps**:
1. [ ] Copy code từ `.ai-knowledge/commons/04-code-examples.md` section 4.1.2
2. [ ] Verify imports:
   - [ ] `use Ramsey\Uuid\Uuid as RamseyUuid;`
   - [ ] `use Ramsey\Uuid\UuidInterface;`
   - [ ] `use InvalidArgumentException;`
   - [ ] `use Stringable;`
3. [ ] Verify class structure:
   - [ ] `declare(strict_types=1);` ở đầu file
   - [ ] `final class Uuid`
   - [ ] `implements Stringable`
   - [ ] Private property `UuidInterface $value`
   - [ ] Private constructor
   - [ ] Static factory method `fromString(string $value): self`
   - [ ] Static factory method `generate(): self`
   - [ ] Method `__toString(): string`
   - [ ] Method `toString(): string`
   - [ ] Method `equals(self $other): bool`
4. [ ] Verify validation logic trong `fromString()`
5. [ ] Verify `generate()` sử dụng `RamseyUuid::uuid4()`

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Có static factory methods: `fromString()`, `generate()`
- [ ] ✅ Validate UUID format trong `fromString()`
- [ ] ✅ Implement `Stringable` interface
- [ ] ✅ Có method `equals()` sử dụng Ramsey UUID comparison
- [ ] ✅ Có explicit method `toString()` ngoài `__toString()`
- [ ] ✅ Tất cả methods có PHPDoc comments
- [ ] ✅ Type hints đầy đủ

**Verification**:
- [ ] Syntax check: `php -l app/SharedKernel/Domain/ValueObjects/Uuid.php`
- [ ] IDE không có errors/warnings
- [ ] Code formatting: `./vendor/bin/pint app/SharedKernel/Domain/ValueObjects/Uuid.php`

---

#### Subtask 1.1.2.4: Write Unit Tests

**Estimated Time**: 90-120 phút

**Steps**:
1. [ ] Tạo test file:
   ```bash
   touch tests/Unit/SharedKernel/Domain/ValueObjects/UuidTest.php
   ```
2. [ ] Copy test code từ section 6 trong task này
3. [ ] Implement test `test_valid_uuid_can_be_created_from_string()`
4. [ ] Implement test `test_new_uuid_can_be_generated()`
5. [ ] Implement test `test_invalid_uuid_throws_exception()`
6. [ ] Implement test `test_equals_method()`
7. [ ] Implement test `test_to_string_method()`
8. [ ] Thêm edge case tests:
   - [ ] Test với UUID v4 format
   - [ ] Test với UUID v1 format (nếu cần)
   - [ ] Test với UUID không có dashes (should fail)
   - [ ] Test với empty string (should fail)
   - [ ] Test với null (should fail - type error)
   - [ ] Test equals với different UUID instances same value
   - [ ] Test equals với different UUID values

**Test Execution**:
```bash
# Run specific test file
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/UuidTest.php

# Run with coverage
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/UuidTest.php --coverage
```

**Test Coverage Requirements**:
- [ ] ✅ Test tất cả public methods
- [ ] ✅ Test edge cases (invalid format, empty, null)
- [ ] ✅ Test equals method
- [ ] ✅ Coverage >= 95%
- [ ] ✅ Tất cả tests pass

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] No skipped tests

---

#### Subtask 1.1.2.5: Integration Test với Database

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Tạo test migration để test UUID storage:
   ```bash
   php artisan make:migration test_uuid_storage
   ```
2. [ ] Tạo test table với UUID column
3. [ ] Write integration test:
   ```php
   public function test_uuid_can_be_stored_in_database(): void
   {
       $uuid = Uuid::generate();
       DB::table('test_table')->insert(['id' => $uuid->toString()]);
       $stored = DB::table('test_table')->where('id', $uuid->toString())->first();
       $this->assertNotNull($stored);
   }
   ```
4. [ ] Run integration test
5. [ ] Clean up test table

**Verification**:
- [ ] UUID có thể lưu vào database
- [ ] UUID có thể query từ database
- [ ] No data loss khi serialize/deserialize

---

#### Subtask 1.1.2.6: Code Formatting và Linting

**Estimated Time**: 15 phút

**Steps**:
1. [ ] Run Laravel Pint
2. [ ] Run PHPStan (nếu có)
3. [ ] Fix any issues
4. [ ] Verify consistency

**Verification**:
- [ ] Pint không có errors
- [ ] PHPStan không có errors
- [ ] Code style nhất quán

---

#### Subtask 1.1.2.7: Manual Testing

**Estimated Time**: 20 phút

**Steps**:
1. [ ] Tạo test script:
   ```php
   // test_uuid.php
   require __DIR__ . '/vendor/autoload.php';
   
   use App\SharedKernel\Domain\ValueObjects\Uuid;
   
   // Test generate
   $uuid1 = Uuid::generate();
   echo "Generated UUID: " . $uuid1 . "\n";
   
   // Test fromString
   $uuid2 = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
   echo "From string: " . $uuid2 . "\n";
   
   // Test equals
   $uuid3 = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
   echo "Equals: " . ($uuid2->equals($uuid3) ? 'true' : 'false') . "\n";
   ```
2. [ ] Run và verify output
3. [ ] Test exception cases
4. [ ] Clean up

**Verification**:
- [ ] Manual tests pass
- [ ] Output đúng như mong đợi

---

#### Subtask 1.1.2.8: Commit và Code Review

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Stage files
2. [ ] Commit với message:
   ```bash
   git commit -m "feat(SharedKernel): add Uuid value object

   - Add Uuid value object using ramsey/uuid library
   - Add fromString() and generate() factory methods
   - Add equals() method for comparison
   - Add unit tests with 95%+ coverage
   - Add integration tests for database storage"
   ```
3. [ ] Push to branch
4. [ ] Request code review

**Verification**:
- [ ] Commit successful
- [ ] PR created (nếu cần)
- [ ] Code review requested
- [ ] CI/CD passes

---

#### Summary Checklist cho Task 1.1.2

- [ ] ✅ Dependencies installed
- [ ] ✅ File Uuid.php được tạo và implement đầy đủ
- [ ] ✅ Unit tests được viết và pass
- [ ] ✅ Integration tests pass
- [ ] ✅ Code coverage >= 95%
- [ ] ✅ Code formatting và linting pass
- [ ] ✅ Manual testing completed
- [ ] ✅ Code committed và reviewed
- [ ] ✅ Ready for next task

**Total Estimated Time**: 3-4 giờ
   - [ ] ✅ Class là `final`
   - [ ] ✅ Constructor là `private`
   - [ ] ✅ Có static factory methods: `fromString()`, `generate()`
   - [ ] ✅ Validate UUID format trong `fromString()`
   - [ ] ✅ Implement `Stringable` interface
   - [ ] ✅ Có method `equals()` sử dụng Ramsey UUID comparison
   - [ ] ✅ Có explicit method `toString()` ngoài `__toString()`

4. **Best Practices**
   - ✅ **Use Ramsey UUID**: Sử dụng thư viện đã được test kỹ thay vì tự implement
   - ✅ **Type safety**: Wrap Ramsey UUID trong Value Object để có type safety
   - ✅ **Consistency**: Tất cả IDs trong hệ thống sẽ sử dụng Uuid Value Object

5. **Potential Pitfalls**
   - ⚠️ **Version compatibility**: Đảm bảo version của `ramsey/uuid` tương thích với PHP version
   - ⚠️ **Performance**: UUID generation có thể chậm hơn auto-increment IDs, nhưng đây là trade-off cho distributed systems
   - ⚠️ **Database storage**: Cần đảm bảo database column type phù hợp (CHAR(36) hoặc UUID type nếu database hỗ trợ)

6. **Unit Tests**

   **File**: `tests/Unit/SharedKernel/Domain/ValueObjects/UuidTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

   use App\SharedKernel\Domain\ValueObjects\Uuid;
   use InvalidArgumentException;
   use PHPUnit\Framework\TestCase;

   final class UuidTest extends TestCase
   {
       /**
        * Test that valid UUID can be created from string.
        */
       public function test_valid_uuid_can_be_created_from_string(): void
       {
           $uuidString = '550e8400-e29b-41d4-a716-446655440000';
           $uuid = Uuid::fromString($uuidString);

           $this->assertInstanceOf(Uuid::class, $uuid);
           $this->assertEquals($uuidString, (string) $uuid);
       }

       /**
        * Test that new UUID can be generated.
        */
       public function test_new_uuid_can_be_generated(): void
       {
           $uuid1 = Uuid::generate();
           $uuid2 = Uuid::generate();

           $this->assertInstanceOf(Uuid::class, $uuid1);
           $this->assertInstanceOf(Uuid::class, $uuid2);
           $this->assertNotEquals((string) $uuid1, (string) $uuid2);
       }

       /**
        * Test that invalid UUID throws exception.
        */
       public function test_invalid_uuid_throws_exception(): void
       {
           $this->expectException(InvalidArgumentException::class);
           $this->expectExceptionMessage('Invalid UUID format');

           Uuid::fromString('not-a-uuid');
       }

       /**
        * Test equals method.
        */
       public function test_equals_method(): void
       {
           $uuidString = '550e8400-e29b-41d4-a716-446655440000';
           $uuid1 = Uuid::fromString($uuidString);
           $uuid2 = Uuid::fromString($uuidString);
           $uuid3 = Uuid::generate();

           $this->assertTrue($uuid1->equals($uuid2));
           $this->assertFalse($uuid1->equals($uuid3));
       }

       /**
        * Test toString method.
        */
       public function test_to_string_method(): void
       {
           $uuidString = '550e8400-e29b-41d4-a716-446655440000';
           $uuid = Uuid::fromString($uuidString);

           $this->assertEquals($uuidString, $uuid->toString());
           $this->assertEquals($uuidString, (string) $uuid);
       }
   }
   ```

---

### Task 1.1.3: Timestamp Value Object

**File**: `app/SharedKernel/Domain/ValueObjects/Timestamp.php`  
**Estimated Time**: 2-3 giờ  
**Priority**: Medium

#### Implementation Steps

1. **Tạo file**
   ```bash
   touch app/SharedKernel/Domain/ValueObjects/Timestamp.php
   ```

2. **Copy code từ code examples**
   - Sử dụng code từ `.ai-knowledge/commons/04-code-examples.md` section 4.1.3

3. **Code Review Checklist**
   - [ ] ✅ Class là `final`
   - [ ] ✅ Sử dụng `DateTimeImmutable` (không phải `DateTime`)
   - [ ] ✅ Có static factory methods: `fromDateTime()`, `fromString()`, `now()`
   - [ ] ✅ Implement `Stringable` với ISO 8601 format
   - [ ] ✅ Có method `format()` để custom format
   - [ ] ✅ Có comparison methods: `equals()`, `isBefore()`, `isAfter()`
   - [ ] ✅ Hỗ trợ timezone

4. **Best Practices**
   - ✅ **Use DateTimeImmutable**: Đảm bảo immutability
   - ✅ **ISO 8601 format**: Standard format cho serialization
   - ✅ **Timezone awareness**: Luôn xử lý timezone đúng cách

5. **Potential Pitfalls**
   - ⚠️ **Timezone handling**: Đảm bảo timezone được xử lý đúng trong toàn bộ hệ thống
   - ⚠️ **Performance**: DateTimeImmutable có thể chậm hơn DateTime một chút, nhưng đáng giá cho immutability
   - ⚠️ **Database storage**: Đảm bảo database column type phù hợp (TIMESTAMP hoặc DATETIME)

6. **Unit Tests**

   **File**: `tests/Unit/SharedKernel/Domain/ValueObjects/TimestampTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Unit\SharedKernel\Domain\ValueObjects;

   use App\SharedKernel\Domain\ValueObjects\Timestamp;
   use DateTimeImmutable;
   use InvalidArgumentException;
   use PHPUnit\Framework\TestCase;

   final class TimestampTest extends TestCase
   {
       /**
        * Test that timestamp can be created from DateTimeImmutable.
        */
       public function test_timestamp_can_be_created_from_datetime(): void
       {
           $dateTime = new DateTimeImmutable('2024-01-01 12:00:00');
           $timestamp = Timestamp::fromDateTime($dateTime);

           $this->assertInstanceOf(Timestamp::class, $timestamp);
       }

       /**
        * Test that timestamp can be created from string.
        */
       public function test_timestamp_can_be_created_from_string(): void
       {
           $timestamp = Timestamp::fromString('2024-01-01T12:00:00+00:00');

           $this->assertInstanceOf(Timestamp::class, $timestamp);
       }

       /**
        * Test that current timestamp can be created.
        */
       public function test_current_timestamp_can_be_created(): void
       {
           $timestamp = Timestamp::now();

           $this->assertInstanceOf(Timestamp::class, $timestamp);
       }

       /**
        * Test that invalid string throws exception.
        */
       public function test_invalid_string_throws_exception(): void
       {
           $this->expectException(InvalidArgumentException::class);

           Timestamp::fromString('invalid-date');
       }

       /**
        * Test equals method.
        */
       public function test_equals_method(): void
       {
           $timestamp1 = Timestamp::fromString('2024-01-01T12:00:00+00:00');
           $timestamp2 = Timestamp::fromString('2024-01-01T12:00:00+00:00');
           $timestamp3 = Timestamp::fromString('2024-01-01T13:00:00+00:00');

           $this->assertTrue($timestamp1->equals($timestamp2));
           $this->assertFalse($timestamp1->equals($timestamp3));
       }

       /**
        * Test isBefore method.
        */
       public function test_is_before_method(): void
       {
           $timestamp1 = Timestamp::fromString('2024-01-01T12:00:00+00:00');
           $timestamp2 = Timestamp::fromString('2024-01-01T13:00:00+00:00');

           $this->assertTrue($timestamp1->isBefore($timestamp2));
           $this->assertFalse($timestamp2->isBefore($timestamp1));
       }

       /**
        * Test isAfter method.
        */
       public function test_is_after_method(): void
       {
           $timestamp1 = Timestamp::fromString('2024-01-01T12:00:00+00:00');
           $timestamp2 = Timestamp::fromString('2024-01-01T13:00:00+00:00');

           $this->assertTrue($timestamp2->isAfter($timestamp1));
           $this->assertFalse($timestamp1->isAfter($timestamp2));
       }

       /**
        * Test format method.
        */
       public function test_format_method(): void
       {
           $timestamp = Timestamp::fromString('2024-01-01T12:00:00+00:00');

           $this->assertEquals('2024-01-01', $timestamp->format('Y-m-d'));
       }
   }
   ```

---

### Task 1.1.4: Domain Exceptions

**Files**:
- `app/SharedKernel/Domain/Exceptions/DomainException.php`
- `app/SharedKernel/Domain/Exceptions/EntityNotFoundException.php`
- `app/SharedKernel/Domain/Exceptions/InvalidArgumentException.php`

**Estimated Time**: 1-2 giờ  
**Priority**: High

#### Implementation Steps

1. **Tạo DomainException (Base Exception)**

   **File**: `app/SharedKernel/Domain/Exceptions/DomainException.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Domain\Exceptions;

   use RuntimeException;

   /**
    * Base exception for domain layer.
    * All domain-specific exceptions should extend this class.
    */
   class DomainException extends RuntimeException
   {
       /**
        * @param string $message
        * @param int $code
        * @param \Throwable|null $previous
        */
       public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
       {
           parent::__construct($message, $code, $previous);
       }
   }
   ```

2. **Tạo EntityNotFoundException**

   **File**: `app/SharedKernel/Domain/Exceptions/EntityNotFoundException.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Domain\Exceptions;

   /**
    * Exception thrown when an entity is not found.
    */
   final class EntityNotFoundException extends DomainException
   {
       /**
        * @param string $entityType
        * @param string $identifier
        */
       public function __construct(string $entityType, string $identifier)
       {
           parent::__construct("{$entityType} not found with identifier: {$identifier}");
       }

       /**
        * Create exception for user not found.
        *
        * @param string $userId
        * @return self
        */
       public static function user(string $userId): self
       {
           return new self('User', $userId);
       }

       /**
        * Create exception for role not found.
        *
        * @param string $roleId
        * @return self
        */
       public static function role(string $roleId): self
       {
           return new self('Role', $roleId);
       }
   }
   ```

3. **Tạo InvalidArgumentException**

   **File**: `app/SharedKernel/Domain/Exceptions/InvalidArgumentException.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Domain\Exceptions;

   /**
    * Exception thrown when an invalid argument is provided.
    */
   final class InvalidArgumentException extends DomainException
   {
       /**
        * @param string $message
        * @param int $code
        * @param \Throwable|null $previous
        */
       public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
       {
           parent::__construct($message, $code, $previous);
       }
   }
   ```

4. **Code Review Checklist**
   - [ ] ✅ DomainException extends RuntimeException (không phải Exception)
   - [ ] ✅ EntityNotFoundException có factory methods cho các entity types phổ biến
   - [ ] ✅ Exception messages rõ ràng và có ý nghĩa
   - [ ] ✅ InvalidArgumentException có thể wrap previous exception

5. **Unit Tests**

   **File**: `tests/Unit/SharedKernel/Domain/Exceptions/EntityNotFoundExceptionTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Unit\SharedKernel\Domain\Exceptions;

   use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;
   use PHPUnit\Framework\TestCase;

   final class EntityNotFoundExceptionTest extends TestCase
   {
       public function test_exception_message_contains_entity_type_and_identifier(): void
       {
           $exception = new EntityNotFoundException('User', '123');

           $this->assertStringContainsString('User', $exception->getMessage());
           $this->assertStringContainsString('123', $exception->getMessage());
       }

       public function test_user_factory_method(): void
       {
           $exception = EntityNotFoundException::user('123');

           $this->assertInstanceOf(EntityNotFoundException::class, $exception);
           $this->assertStringContainsString('User', $exception->getMessage());
       }

       public function test_role_factory_method(): void
       {
           $exception = EntityNotFoundException::role('456');

           $this->assertInstanceOf(EntityNotFoundException::class, $exception);
           $this->assertStringContainsString('Role', $exception->getMessage());
       }
   }
   ```

---

### Task 1.1.5: Domain Interfaces

**Files**:
- `app/SharedKernel/Domain/Interfaces/EventDispatcherInterface.php`
- `app/SharedKernel/Domain/Interfaces/ClockInterface.php`

**Estimated Time**: 1-2 giờ  
**Priority**: High

#### Implementation Steps

1. **Tạo EventDispatcherInterface**

   **File**: `app/SharedKernel/Domain/Interfaces/EventDispatcherInterface.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Domain\Interfaces;

   /**
    * Interface for dispatching domain events.
    * This allows easy mocking in tests and decoupling from Laravel's event system.
    */
   interface EventDispatcherInterface
   {
       /**
        * Dispatch a domain event.
        *
        * @param object $event
        * @return void
        */
       public function dispatch(object $event): void;

       /**
        * Dispatch multiple domain events.
        *
        * @param array<object> $events
        * @return void
        */
       public function dispatchMany(array $events): void;
   }
   ```

2. **Tạo ClockInterface**

   **File**: `app/SharedKernel/Domain/Interfaces/ClockInterface.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Domain\Interfaces;

   use App\SharedKernel\Domain\ValueObjects\Timestamp;

   /**
    * Interface for getting current time.
    * Useful for testing and ensuring time consistency.
    */
   interface ClockInterface
   {
       /**
        * Get current timestamp.
        *
        * @param string|null $timezone
        * @return Timestamp
        */
       public function now(?string $timezone = null): Timestamp;
   }
   ```

3. **Code Review Checklist**
   - [ ] ✅ Interfaces chỉ định nghĩa contracts, không có implementation
   - [ ] ✅ EventDispatcherInterface có method `dispatchMany()` để optimize batch operations
   - [ ] ✅ ClockInterface return Timestamp Value Object (không phải DateTime)
   - [ ] ✅ ClockInterface hỗ trợ timezone parameter

---

## Task 1.2: Shared Kernel Infrastructure Layer

### Mục tiêu

Implement các interfaces đã định nghĩa trong Domain Layer:
- LaravelEventDispatcher (implement EventDispatcherInterface)
- SystemClock và FixedClock (implement ClockInterface)
- SharedKernelServiceProvider (đăng ký bindings)

### Cấu trúc thư mục

```
app/SharedKernel/
└── Infrastructure/
    ├── EventDispatcher/
    │   └── LaravelEventDispatcher.php
    ├── Clock/
    │   ├── SystemClock.php
    │   └── FixedClock.php
    └── Providers/
        └── SharedKernelServiceProvider.php
```

---

### Task 1.2.1: LaravelEventDispatcher

**File**: `app/SharedKernel/Infrastructure/EventDispatcher/LaravelEventDispatcher.php`  
**Estimated Time**: 1 giờ  
**Priority**: High

#### Implementation Steps

1. **Copy code từ code examples**
   - Sử dụng code từ `.ai-knowledge/commons/04-code-examples.md` section 4.3.1

2. **Code Review Checklist**
   - [ ] ✅ Implement EventDispatcherInterface
   - [ ] ✅ Inject Laravel's Dispatcher (không sử dụng facade)
   - [ ] ✅ Method `dispatchMany()` loop qua array và gọi `dispatch()` cho mỗi event
   - [ ] ✅ Class là `final`

3. **Best Practices**
   - ✅ **Dependency Injection**: Inject Dispatcher thay vì sử dụng `event()` helper
   - ✅ **Type hinting**: Sử dụng `Illuminate\Contracts\Events\Dispatcher` interface

---

### Task 1.2.2: Clock Implementations

**Files**:
- `app/SharedKernel/Infrastructure/Clock/SystemClock.php`
- `app/SharedKernel/Infrastructure/Clock/FixedClock.php`

**Estimated Time**: 1-2 giờ  
**Priority**: Medium

#### Implementation Steps

1. **SystemClock**
   - Copy code từ code examples section 4.3.2
   - Đảm bảo sử dụng `Timestamp::now()` trong implementation

2. **FixedClock (cho testing)**
   - Copy code từ code examples section 4.3.2
   - Đảm bảo luôn return cùng một Timestamp instance

3. **Code Review Checklist**
   - [ ] ✅ SystemClock implement ClockInterface
   - [ ] ✅ FixedClock implement ClockInterface
   - [ ] ✅ FixedClock có constructor nhận Timestamp
   - [ ] ✅ FixedClock luôn return cùng Timestamp (useful cho testing)

---

### Task 1.2.3: SharedKernelServiceProvider

**File**: `app/SharedKernel/Infrastructure/Providers/SharedKernelServiceProvider.php`  
**Estimated Time**: 1 giờ  
**Priority**: High

#### Implementation Steps

1. **Tạo Service Provider**
   - Copy code từ code examples section 4.4

2. **Đăng ký trong config/app.php**
   ```php
   'providers' => [
       // ...
       App\SharedKernel\Infrastructure\Providers\SharedKernelServiceProvider::class,
   ],
   ```

3. **Code Review Checklist**
   - [ ] ✅ Bind EventDispatcherInterface với LaravelEventDispatcher
   - [ ] ✅ Bind ClockInterface với SystemClock
   - [ ] ✅ Sử dụng `singleton()` để đảm bảo chỉ có một instance
   - [ ] ✅ Service Provider được đăng ký trong `config/app.php`

4. **Testing Service Provider**

   **File**: `tests/Unit/SharedKernel/Infrastructure/Providers/SharedKernelServiceProviderTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Unit\SharedKernel\Infrastructure\Providers;

   use App\SharedKernel\Domain\Interfaces\ClockInterface;
   use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;
   use App\SharedKernel\Infrastructure\Providers\SharedKernelServiceProvider;
   use Illuminate\Foundation\Testing\RefreshDatabase;
   use Illuminate\Support\Facades\App;
   use Tests\TestCase;

   final class SharedKernelServiceProviderTest extends TestCase
   {
       use RefreshDatabase;

       public function test_event_dispatcher_interface_is_bound(): void
       {
           $dispatcher = App::make(EventDispatcherInterface::class);

           $this->assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
       }

       public function test_clock_interface_is_bound(): void
       {
           $clock = App::make(ClockInterface::class);

           $this->assertInstanceOf(ClockInterface::class, $clock);
       }

       public function test_event_dispatcher_is_singleton(): void
       {
           $dispatcher1 = App::make(EventDispatcherInterface::class);
           $dispatcher2 = App::make(EventDispatcherInterface::class);

           $this->assertSame($dispatcher1, $dispatcher2);
       }

       public function test_clock_is_singleton(): void
       {
           $clock1 = App::make(ClockInterface::class);
           $clock2 = App::make(ClockInterface::class);

           $this->assertSame($clock1, $clock2);
       }
   }
   ```

---

## Task 1.3: Outbox Pattern Infrastructure

### Mục tiêu

Implement Transactional Outbox Pattern để đảm bảo tính nhất quán dữ liệu khi giao tiếp giữa các Bounded Contexts thông qua Domain Events.

### Cấu trúc thư mục

```
app/SharedKernel/
└── Infrastructure/
    ├── Outbox/
    │   ├── OutboxEvent.php (Eloquent Model)
    │   └── OutboxEventRepository.php
    └── Console/
        └── ProcessOutboxCommand.php
```

---

### Task 1.3.1: Outbox Migration

**File**: `database/migrations/XXXX_create_outbox_events_table.php`  
**Estimated Time**: 30 phút  
**Priority**: High

#### Implementation Steps

1. **Tạo migration**
   ```bash
   php artisan make:migration create_outbox_events_table
   ```

2. **Implement migration**

   ```php
   <?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('outbox_events', function (Blueprint $table) {
               $table->uuid('id')->primary();
               $table->string('aggregate_type', 100)->index();
               $table->uuid('aggregate_id')->index();
               $table->string('event_type', 255)->index();
               $table->json('payload');
               $table->timestamp('created_at')->useCurrent();
               $table->timestamp('processed_at')->nullable()->index();
           });

           // Index for querying unprocessed events
           Schema::table('outbox_events', function (Blueprint $table) {
               $table->index(['processed_at', 'created_at']);
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('outbox_events');
       }
   };
   ```

3. **Code Review Checklist**
   - [ ] ✅ Primary key là UUID (không phải auto-increment)
   - [ ] ✅ Có indexes cho các columns thường query: `aggregate_type`, `aggregate_id`, `event_type`, `processed_at`
   - [ ] ✅ Composite index cho query unprocessed events: `(processed_at, created_at)`
   - [ ] ✅ Column `payload` là JSON type
   - [ ] ✅ Column `processed_at` nullable để đánh dấu event chưa xử lý

4. **Best Practices**
   - ✅ **Indexes**: Đảm bảo có indexes phù hợp để optimize queries
   - ✅ **UUID**: Sử dụng UUID thay vì auto-increment để tránh conflicts trong distributed systems
   - ✅ **JSON payload**: Lưu event data dưới dạng JSON để flexible

5. **Run Migration**
   ```bash
   php artisan migrate
   ```

---

### Task 1.3.2: OutboxEvent Model

**File**: `app/SharedKernel/Infrastructure/Outbox/OutboxEvent.php`  
**Estimated Time**: 1 giờ  
**Priority**: High

#### Implementation Steps

1. **Tạo Eloquent Model**
   ```bash
   php artisan make:model SharedKernel/Infrastructure/Outbox/OutboxEvent
   ```

2. **Implement Model**

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Infrastructure\Outbox;

   use Illuminate\Database\Eloquent\Model;

   /**
    * Eloquent model for outbox events table.
    * This model is used only in Infrastructure layer for persistence.
    */
   final class OutboxEvent extends Model
   {
       public $incrementing = false;
       protected $keyType = 'string';

       protected $fillable = [
           'id',
           'aggregate_type',
           'aggregate_id',
           'event_type',
           'payload',
           'created_at',
           'processed_at',
       ];

       protected $casts = [
           'payload' => 'array',
           'created_at' => 'datetime',
           'processed_at' => 'datetime',
       ];

       /**
        * Check if event is processed.
        *
        * @return bool
        */
       public function isProcessed(): bool
       {
           return $this->processed_at !== null;
       }

       /**
        * Mark event as processed.
        *
        * @return void
        */
       public function markAsProcessed(): void
       {
           $this->processed_at = now();
           $this->save();
       }
   }
   ```

3. **Code Review Checklist**
   - [ ] ✅ Model là `final`
   - [ ] ✅ `$incrementing = false` và `$keyType = 'string'` cho UUID primary key
   - [ ] ✅ `payload` được cast thành array
   - [ ] ✅ Có helper methods: `isProcessed()`, `markAsProcessed()`
   - [ ] ✅ Table name đúng: `outbox_events`

---

### Task 1.3.3: ProcessOutboxCommand

**File**: `app/SharedKernel/Infrastructure/Console/ProcessOutboxCommand.php`  
**Estimated Time**: 2-3 giờ  
**Priority**: High

#### Implementation Steps

1. **Tạo Command**
   ```bash
   php artisan make:command SharedKernel/Infrastructure/Console/ProcessOutboxCommand
   ```

2. **Implement Command**

   ```php
   <?php

   declare(strict_types=1);

   namespace App\SharedKernel\Infrastructure\Console;

   use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
   use Illuminate\Console\Command;
   use Illuminate\Support\Facades\DB;
   use Illuminate\Support\Facades\Event;

   /**
    * Command to process outbox events.
    * This command should be run periodically (e.g., every minute) via Laravel Scheduler.
    */
   final class ProcessOutboxCommand extends Command
   {
       protected $signature = 'outbox:process {--limit=100 : Number of events to process}';
       protected $description = 'Process unprocessed events from outbox';

       public function handle(): int
       {
           $limit = (int) $this->option('limit');
           $processedCount = 0;

           OutboxEvent::whereNull('processed_at')
               ->orderBy('created_at')
               ->limit($limit)
               ->chunk(100, function ($events) use (&$processedCount) {
                   foreach ($events as $outboxEvent) {
                       try {
                           $this->processEvent($outboxEvent);
                           $outboxEvent->markAsProcessed();
                           $processedCount++;
                       } catch (\Throwable $e) {
                           $this->error("Failed to process event {$outboxEvent->id}: {$e->getMessage()}");
                           // Log error but continue processing other events
                           \Log::error('Outbox event processing failed', [
                               'event_id' => $outboxEvent->id,
                               'error' => $e->getMessage(),
                               'trace' => $e->getTraceAsString(),
                           ]);
                       }
                   }
               });

           $this->info("Processed {$processedCount} events");

           return Command::SUCCESS;
       }

       /**
        * Process a single outbox event.
        *
        * @param OutboxEvent $outboxEvent
        * @return void
        */
       private function processEvent(OutboxEvent $outboxEvent): void
       {
           // Reconstitute domain event from payload
           $eventClass = $outboxEvent->event_type;

           if (!class_exists($eventClass)) {
               throw new \RuntimeException("Event class {$eventClass} does not exist");
           }

           // Create event instance from payload
           // This assumes event has a static factory method or constructor that accepts array
           $event = $this->reconstituteEvent($eventClass, $outboxEvent->payload);

           // Dispatch event using Laravel's event system
           Event::dispatch($event);
       }

       /**
        * Reconstitute domain event from payload.
        *
        * @param string $eventClass
        * @param array<string, mixed> $payload
        * @return object
        */
       private function reconstituteEvent(string $eventClass, array $payload): object
       {
           // Try to use reflection to create event instance
           $reflection = new \ReflectionClass($eventClass);

           // Check if event has a static factory method
           if ($reflection->hasMethod('fromPayload')) {
               return $eventClass::fromPayload($payload);
           }

           // Otherwise, try to create using constructor
           if ($reflection->getConstructor() !== null) {
               return $reflection->newInstanceArgs($this->mapPayloadToConstructorArgs($reflection, $payload));
           }

           throw new \RuntimeException("Cannot reconstitute event {$eventClass} from payload");
       }

       /**
        * Map payload array to constructor arguments.
        *
        * @param \ReflectionClass $reflection
        * @param array<string, mixed> $payload
        * @return array<mixed>
        */
       private function mapPayloadToConstructorArgs(\ReflectionClass $reflection, array $payload): array
       {
           $constructor = $reflection->getConstructor();
           if ($constructor === null) {
               return [];
           }

           $args = [];
           foreach ($constructor->getParameters() as $parameter) {
               $paramName = $parameter->getName();
               if (isset($payload[$paramName])) {
                   $args[] = $payload[$paramName];
               } elseif ($parameter->isDefaultValueAvailable()) {
                   $args[] = $parameter->getDefaultValue();
               } else {
                   throw new \RuntimeException("Missing required parameter {$paramName} for event {$reflection->getName()}");
               }
           }

           return $args;
       }
   }
   ```

3. **Đăng ký trong Scheduler**

   **File**: `app/Console/Kernel.php`

   ```php
   protected function schedule(Schedule $schedule): void
   {
       $schedule->command('outbox:process')
           ->everyMinute()
           ->withoutOverlapping()
           ->runInBackground();
   }
   ```

4. **Code Review Checklist**
   - [ ] ✅ Command có option `--limit` để giới hạn số events xử lý mỗi lần
   - [ ] ✅ Sử dụng `chunk()` để xử lý events theo batch
   - [ ] ✅ Có error handling: log errors nhưng tiếp tục xử lý events khác
   - [ ] ✅ Có method `reconstituteEvent()` để tạo lại event từ payload
   - [ ] ✅ Command được đăng ký trong scheduler với `withoutOverlapping()`

5. **Best Practices**
   - ✅ **Batch processing**: Xử lý events theo batch để tránh memory issues
   - ✅ **Error handling**: Không fail toàn bộ batch nếu một event fail
   - ✅ **Idempotency**: Đảm bảo command có thể chạy nhiều lần mà không duplicate events
   - ✅ **Without overlapping**: Đảm bảo chỉ có một instance của command chạy tại một thời điểm

6. **Testing Command**

   **File**: `tests/Feature/SharedKernel/Infrastructure/Console/ProcessOutboxCommandTest.php`

   ```php
   <?php

   declare(strict_types=1);

   namespace Tests\Feature\SharedKernel\Infrastructure\Console;

   use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
   use Illuminate\Foundation\Testing\RefreshDatabase;
   use Illuminate\Support\Facades\Event;
   use Tests\TestCase;

   final class ProcessOutboxCommandTest extends TestCase
   {
       use RefreshDatabase;

       public function test_processes_unprocessed_events(): void
       {
           // Arrange: Create unprocessed event
           $outboxEvent = OutboxEvent::create([
               'id' => \Illuminate\Support\Str::uuid()->toString(),
               'aggregate_type' => 'user',
               'aggregate_id' => \Illuminate\Support\Str::uuid()->toString(),
               'event_type' => 'App\Test\Domain\Events\TestEvent',
               'payload' => ['test' => 'data'],
           ]);

           // Act: Run command
           $this->artisan('outbox:process')->assertSuccessful();

           // Assert: Event is marked as processed
           $outboxEvent->refresh();
           $this->assertNotNull($outboxEvent->processed_at);
       }

       public function test_processes_events_in_order(): void
       {
           // Test that events are processed in created_at order
           // Implementation...
       }

       public function test_continues_processing_on_error(): void
       {
           // Test that if one event fails, others are still processed
           // Implementation...
       }
   }
   ```

---

## Task 1.4: Testing & Documentation

### Mục tiêu

- Đảm bảo test coverage >= 90% cho Shared Kernel
- Tạo documentation đầy đủ
- Setup CI/CD để chạy tests tự động

---

### Task 1.4.1: Test Coverage

**Estimated Time**: 2-3 giờ  
**Priority**: High

#### Requirements

- [ ] ✅ Unit tests cho tất cả Value Objects (Email, Uuid, Timestamp)
- [ ] ✅ Unit tests cho tất cả Exceptions
- [ ] ✅ Unit tests cho Service Provider bindings
- [ ] ✅ Feature tests cho ProcessOutboxCommand
- [ ] ✅ Integration tests cho Outbox Pattern
- [ ] ✅ Test coverage >= 90%

#### Run Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

---

### Task 1.6.2: Documentation

**Estimated Time**: 1-2 giờ  
**Priority**: Medium

#### Documentation Requirements

1. **README cho Shared Kernel**
   - File: `app/SharedKernel/README.md`
   - Mô tả mục đích và cách sử dụng Shared Kernel
   - Examples sử dụng các Value Objects
   - Examples sử dụng Outbox Pattern

2. **Code Comments**
   - Đảm bảo tất cả public methods có PHPDoc comments
   - Comments bằng tiếng Anh theo convention

3. **Architecture Decision Records (ADR)**
   - File: `.ai-knowledge/adr/001-shared-kernel-design.md`
   - Giải thích tại sao chọn Shared Kernel approach
   - Giải thích tại sao chọn Outbox Pattern

---

## Code Review Checklist (Tổng thể)

Trước khi merge Phase 1, đảm bảo:

### Code Quality
- [ ] ✅ Tất cả files có `declare(strict_types=1);`
- [ ] ✅ Tất cả classes là `final` (trừ base exceptions)
- [ ] ✅ Tất cả public methods có PHPDoc comments
- [ ] ✅ Code tuân thủ Laravel Pint formatting
- [ ] ✅ Không có unused imports hoặc variables

### Architecture
- [ ] ✅ Domain Layer không có dependencies vào Laravel
- [ ] ✅ Infrastructure Layer implement đúng interfaces từ Domain Layer
- [ ] ✅ Service Provider đăng ký đúng bindings
- [ ] ✅ Outbox Pattern được implement đúng theo architecture document

### Testing
- [ ] ✅ Test coverage >= 90%
- [ ] ✅ Tất cả tests pass
- [ ] ✅ Tests có meaningful assertions
- [ ] ✅ Tests cover edge cases

### Documentation
- [ ] ✅ README cho Shared Kernel
- [ ] ✅ ADR cho design decisions
- [ ] ✅ Code comments đầy đủ

---

## Troubleshooting Guide

### Issue 1: Service Provider không được load

**Symptoms**: `BindingResolutionException` khi resolve interfaces

**Root Causes**:
- Service Provider chưa được đăng ký trong `config/app.php`
- Config cache chưa được clear
- Namespace không đúng

**Solutions**:
1. Kiểm tra Service Provider đã được đăng ký trong `config/app.php`:
   ```php
   'providers' => [
       // ...
       App\SharedKernel\Infrastructure\Providers\SharedKernelServiceProvider::class,
   ],
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Kiểm tra namespace của Service Provider đúng
4. Verify Service Provider class exists:
   ```bash
   ls -la app/SharedKernel/Infrastructure/Providers/SharedKernelServiceProvider.php
   ```
5. Check autoload:
   ```bash
   composer dump-autoload
   ```

**Prevention**:
- ✅ Luôn verify Service Provider được đăng ký sau khi tạo
- ✅ Clear cache sau khi thay đổi config
- ✅ Run tests để verify bindings work

---

### Issue 2: Outbox Command không chạy

**Symptoms**: Events không được process

**Root Causes**:
- Command chưa được đăng ký trong scheduler
- Scheduler không chạy
- Events không được lưu vào outbox đúng cách

**Solutions**:
1. Kiểm tra command đã được đăng ký trong `app/Console/Kernel.php`:
   ```php
   protected function schedule(Schedule $schedule): void
   {
       $schedule->command('outbox:process')
           ->everyMinute()
           ->withoutOverlapping()
           ->runInBackground();
   }
   ```
2. Kiểm tra scheduler đang chạy:
   ```bash
   php artisan schedule:work
   ```
3. Test command manually:
   ```bash
   php artisan outbox:process
   ```
4. Kiểm tra logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
5. Kiểm tra events trong database:
   ```sql
   SELECT * FROM outbox_events WHERE processed_at IS NULL;
   ```

**Prevention**:
- ✅ Test command manually sau khi implement
- ✅ Verify scheduler configuration
- ✅ Monitor logs regularly

---

### Issue 3: UUID validation fails

**Symptoms**: `InvalidArgumentException` khi tạo Uuid từ string

**Root Causes**:
- UUID format không đúng (thiếu dashes)
- Version của ramsey/uuid không tương thích
- String không phải là valid UUID

**Solutions**:
1. Kiểm tra UUID format đúng (có dashes):
   ```php
   // Correct: 550e8400-e29b-41d4-a716-446655440000
   // Wrong: 550e8400e29b41d4a716446655440000
   ```
2. Kiểm tra version của `ramsey/uuid` package:
   ```bash
   composer show ramsey/uuid
   ```
3. Sử dụng `Uuid::generate()` thay vì `fromString()` nếu có thể
4. Validate string trước khi tạo Uuid:
   ```php
   if (!Ramsey\Uuid\Uuid::isValid($string)) {
       throw new InvalidArgumentException("Invalid UUID format");
   }
   ```

**Prevention**:
- ✅ Luôn validate input trước khi tạo Uuid
- ✅ Sử dụng `generate()` khi có thể
- ✅ Test với various UUID formats

---

### Issue 4: Tests fail với "Class not found"

**Symptoms**: `ClassNotFoundException` khi chạy tests

**Root Causes**:
- Autoload chưa được regenerate
- Namespace không đúng
- Class chưa được tạo

**Solutions**:
1. Regenerate autoload:
   ```bash
   composer dump-autoload
   ```
2. Kiểm tra namespace trong test file đúng
3. Kiểm tra class exists:
   ```bash
   ls -la app/SharedKernel/Domain/ValueObjects/Email.php
   ```
4. Clear test cache:
   ```bash
   php artisan test --clear-cache
   ```

**Prevention**:
- ✅ Luôn run `composer dump-autoload` sau khi tạo class mới
- ✅ Verify namespace đúng
- ✅ Run tests thường xuyên

---

### Issue 5: Code formatting issues

**Symptoms**: Pint hoặc PHPStan errors

**Root Causes**:
- Code không follow Laravel conventions
- Missing type hints
- Unused imports

**Solutions**:
1. Run Pint để auto-fix:
   ```bash
   ./vendor/bin/pint app/SharedKernel/
   ```
2. Fix PHPStan errors:
   ```bash
   ./vendor/bin/phpstan analyse app/SharedKernel/
   ```
3. Remove unused imports
4. Add missing type hints

**Prevention**:
- ✅ Run Pint trước khi commit
- ✅ Fix PHPStan errors ngay khi có
- ✅ Follow Laravel conventions

---

### Issue 6: Test coverage không đạt yêu cầu

**Symptoms**: Coverage < 90%

**Root Causes**:
- Thiếu test cases
- Edge cases chưa được test
- Some code paths không được cover

**Solutions**:
1. Identify uncovered code:
   ```bash
   php artisan test --coverage
   ```
2. Add missing test cases
3. Test edge cases
4. Verify coverage:
   ```bash
   php artisan test --coverage --min=90
   ```

**Prevention**:
- ✅ Write tests cùng lúc với implementation
- ✅ Test edge cases
- ✅ Monitor coverage regularly

---

### Issue 7: Migration fails

**Symptoms**: Migration errors khi chạy `php artisan migrate`

**Root Causes**:
- Database connection issues
- Table already exists
- Syntax errors trong migration

**Solutions**:
1. Check database connection:
   ```bash
   php artisan migrate:status
   ```
2. Check if table exists:
   ```sql
   SHOW TABLES LIKE 'outbox_events';
   ```
3. Rollback và migrate lại:
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```
4. Check migration syntax:
   ```bash
   php -l database/migrations/XXXX_create_outbox_events_table.php
   ```

**Prevention**:
- ✅ Test migrations trên local trước
- ✅ Verify migration syntax
- ✅ Backup database trước khi migrate

---

## Common Questions & Answers

### Q1: Có nên commit sau mỗi subtask không?

**A**: Có, nên commit sau mỗi subtask hoàn thành. Điều này giúp:
- Track progress dễ dàng hơn
- Rollback dễ dàng nếu có vấn đề
- Code review dễ dàng hơn với smaller changes
- Backup code thường xuyên

### Q2: Khi nào nên tạo PR?

**A**: Nên tạo PR khi:
- Hoàn thành một task lớn (ví dụ: Task 1.1.1)
- Hoàn thành một milestone
- Cần code review trước khi tiếp tục

### Q3: Làm gì nếu estimate time không đúng?

**A**: 
- Update estimate trong plan
- Document lý do tại sao mất nhiều thời gian hơn
- Adjust timeline nếu cần
- Communicate với team về delays

### Q4: Có cần viết tests cho mọi thứ không?

**A**: Có, nhưng prioritize:
- High priority: Value Objects, Exceptions, Interfaces
- Medium priority: Infrastructure implementations
- Low priority: Simple getters/setters (nếu có)

### Q5: Làm gì nếu gặp blocker?

**A**:
1. Document blocker rõ ràng
2. Try to find solution (research, ask team)
3. Nếu không giải quyết được trong 1-2 giờ, escalate
4. Consider workaround nếu có
5. Update plan với blocker information

---

## Success Criteria

Phase 1 được coi là hoàn thành khi:

- [ ] ✅ Tất cả Value Objects (Email, Uuid, Timestamp) được implement và tested
- [ ] ✅ Tất cả Exceptions được implement và tested
- [ ] ✅ Tất cả Interfaces được implement và tested
- [ ] ✅ Outbox Pattern được implement và tested
- [ ] ✅ Service Provider được đăng ký và tested
- [ ] ✅ Test coverage >= 90%
- [ ] ✅ Documentation đầy đủ
- [ ] ✅ Code review passed
- [ ] ✅ CI/CD pipeline pass

---

## Next Steps (Sau khi hoàn thành Phase 1)

Sau khi Phase 1 hoàn thành và được merge:

1. ✅ Update main branch với Phase 1 changes
2. ✅ Create branch cho Phase 2: `feat/migrate-ddd-phase2`
3. ✅ Review Phase 2 plan với team
4. ✅ Bắt đầu Phase 2: OrganizationalStructure Context

---

## Notes cho Team

- **Không rush**: Phase 1 là foundation, làm đúng ngay từ đầu sẽ tiết kiệm thời gian sau này
- **Test-driven**: Viết tests trước khi implement nếu có thể
- **Code review**: Mỗi PR cần có ít nhất 1 reviewer approve
- **Documentation**: Update documentation ngay khi có thay đổi
- **Communication**: Nếu gặp vấn đề, discuss với team trước khi tiếp tục

---

## Milestones và Progress Tracking

### Milestone 1: Shared Kernel Domain Layer (Value Objects)

**Target**: Hoàn thành Task 1.1.1, 1.1.2, 1.1.3

**Progress Checklist**:
- [ ] ✅ Task 1.1.1: Email Value Object (2-3 giờ)
  - [ ] Subtask 1.1.1.1: Setup và Tạo File
  - [ ] Subtask 1.1.1.2: Implement Email Class
  - [ ] Subtask 1.1.1.3: Write Unit Tests
  - [ ] Subtask 1.1.1.4: Code Formatting và Linting
  - [ ] Subtask 1.1.1.5: Manual Testing
  - [ ] Subtask 1.1.1.6: Commit và Code Review
- [ ] ✅ Task 1.1.2: Uuid Value Object (3-4 giờ)
  - [ ] Subtask 1.1.2.1: Install Dependencies
  - [ ] Subtask 1.1.2.2: Setup và Tạo File
  - [ ] Subtask 1.1.2.3: Implement Uuid Class
  - [ ] Subtask 1.1.2.4: Write Unit Tests
  - [ ] Subtask 1.1.2.5: Integration Test với Database
  - [ ] Subtask 1.1.2.6: Code Formatting và Linting
  - [ ] Subtask 1.1.2.7: Manual Testing
  - [ ] Subtask 1.1.2.8: Commit và Code Review
- [ ] ✅ Task 1.1.3: Timestamp Value Object (2-3 giờ)
- [ ] ✅ Task 1.1.4: Domain Exceptions (1-2 giờ)
- [ ] ✅ Task 1.1.5: Domain Interfaces (1-2 giờ)

**Estimated Time**: 9-14 giờ  
**Status**: ⏳ Not Started / 🔄 In Progress / ✅ Completed

---

### Milestone 2: Shared Kernel Infrastructure Layer

**Target**: Hoàn thành Task 1.2.1, 1.2.2, 1.2.3

**Progress Checklist**:
- [ ] ✅ Task 1.2.1: LaravelEventDispatcher (1 giờ)
- [ ] ✅ Task 1.2.2: Clock Implementations (1-2 giờ)
- [ ] ✅ Task 1.2.3: SharedKernelServiceProvider (1 giờ)

**Estimated Time**: 3-4 giờ  
**Status**: ⏳ Not Started / 🔄 In Progress / ✅ Completed

---

### Milestone 3: Outbox Pattern Infrastructure

**Target**: Hoàn thành Task 1.3.1, 1.3.2, 1.3.3

**Progress Checklist**:
- [ ] ✅ Task 1.3.1: Outbox Migration (30 phút)
- [ ] ✅ Task 1.3.2: OutboxEvent Model (1 giờ)
- [ ] ✅ Task 1.3.3: ProcessOutboxCommand (2-3 giờ)

**Estimated Time**: 3.5-4.5 giờ  
**Status**: ⏳ Not Started / 🔄 In Progress / ✅ Completed

---

### Milestone 4: Testing & Documentation

**Target**: Hoàn thành Task 1.4, 1.5, 1.6

**Progress Checklist**:
- [ ] ✅ Task 1.4: Monitoring & Observability Setup (0.5 ngày)
- [ ] ✅ Task 1.5: Cross-Context Communication Strategy (1 giờ)
- [ ] ✅ Task 1.6.1: Test Coverage (2-3 giờ)
- [ ] ✅ Task 1.6.2: Documentation (1-2 giờ)

**Estimated Time**: 3-5 giờ  
**Status**: ⏳ Not Started / 🔄 In Progress / ✅ Completed

---

## Daily Progress Log Template

**Date**: _______________

**Tasks Completed Today**:
- [ ] Task X.X.X: Description
- [ ] Task X.X.X: Description

**Time Spent**: ___ hours

**Issues Encountered**:
- Issue 1: Description
  - Solution: ...
- Issue 2: Description
  - Solution: ...

**Blockers**:
- Blocker 1: Description
- Blocker 2: Description

**Next Steps**:
- [ ] Task X.X.X: Description
- [ ] Task X.X.X: Description

**Notes**:
- Note 1
- Note 2

---

## Weekly Review Template

**Week**: Week X (Date range)

**Completed This Week**:
- Milestone X: Completed
- Task X.X.X: Completed
- Task X.X.X: Completed

**In Progress**:
- Task X.X.X: X% complete

**Planned for Next Week**:
- Task X.X.X
- Task X.X.X

**Metrics**:
- Total hours spent: ___
- Tasks completed: ___
- Tests written: ___
- Code coverage: ___%
- PRs merged: ___

**Retrospective**:
- What went well: ...
- What could be improved: ...
- Action items: ...

---

## Quick Reference Commands

### Development Commands

```bash
# Run tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Unit/SharedKernel/Domain/ValueObjects/EmailTest.php

# Code formatting
./vendor/bin/pint app/SharedKernel/

# Check syntax
php -l app/SharedKernel/Domain/ValueObjects/Email.php

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Git Commands

```bash
# Create feature branch
git checkout -b feat/migrate-ddd-phase1

# Stage files
git add app/SharedKernel/

# Commit
git commit -m "feat(SharedKernel): description"

# Push
git push origin feat/migrate-ddd-phase1

# Check status
git status
git log --oneline -10
```

### Verification Commands

```bash
# Check PHP version
php -v

# Check Laravel version
php artisan --version

# Check Composer packages
composer show ramsey/uuid
composer show phpunit/phpunit

# Check test coverage
php artisan test --coverage --min=90
```

---

**Kết thúc Phase 1 Plan**
