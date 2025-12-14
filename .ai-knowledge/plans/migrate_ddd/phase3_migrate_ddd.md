# Phase 3: IdentityAccess Context - Kế hoạch Chi tiết

**Tác giả**: Senior Architect (10+ năm kinh nghiệm)  
**Ngày tạo**: 2024  
**Phiên bản**: 1.0  
**Trạng thái**: Draft

## 🚨 SECURITY FIRST - ĐỌC TRƯỚC KHI BẮT ĐẦU

**⚠️ CRITICAL WARNING**: Phase 3 là **SECURITY-CRITICAL**. Bất kỳ lỗi bảo mật nào đều có thể dẫn đến:
- **Data breach** (Rò rỉ dữ liệu)
- **Account compromise** (Tài khoản bị xâm nhập)
- **System compromise** (Hệ thống bị tấn công)
- **Legal liability** (Trách nhiệm pháp lý)
- **Reputation damage** (Tổn hại danh tiếng)

### 🔒 Security là Priority #1

**Mọi quyết định trong Phase 3 phải được đánh giá qua lăng kính bảo mật:**

1. ✅ **Security trước, Performance sau**: Chọn giải pháp an toàn hơn, ngay cả khi chậm hơn một chút
2. ✅ **Security trước, Convenience sau**: Ưu tiên bảo mật hơn là tiện lợi
3. ✅ **Security trước, Cost sau**: Đầu tư vào bảo mật là đầu tư đúng đắn
4. ✅ **When in doubt, choose security**: Khi không chắc chắn, chọn giải pháp an toàn hơn

### 📋 Mandatory Security Requirements

**TRƯỚC KHI BẮT ĐẦU BẤT KỲ TASK NÀO:**

- [ ] ✅ Đã đọc và hiểu **Security Considerations** section (bên dưới)
- [ ] ✅ Đã đọc và hiểu **Security Patterns và Anti-Patterns** section
- [ ] ✅ Đã đọc và hiểu **Security Testing Guide** section
- [ ] ✅ Đã đọc và hiểu **Security Review Checklist** section
- [ ] ✅ Đã hiểu rõ **OWASP Top 10** và cách prevent
- [ ] ✅ Đã setup security testing tools
- [ ] ✅ Đã có security expert available để review code

**KHÔNG BAO GIỜ:**
- ❌ Store plain passwords (bất kỳ đâu)
- ❌ Log passwords hoặc sensitive data
- ❌ Expose sensitive information trong error messages
- ❌ Skip security tests
- ❌ Commit code without security review
- ❌ Use weak hashing algorithms
- ❌ Skip rate limiting
- ❌ Ignore security warnings

**LUÔN LUÔN:**
- ✅ Hash passwords với strong algorithm (bcrypt cost >= 10 hoặc argon2id)
- ✅ Use timing-safe comparisons cho secrets
- ✅ Implement rate limiting cho authentication
- ✅ Use generic error messages
- ✅ Validate ALL inputs
- ✅ Write security tests
- ✅ Review code với security lens
- ✅ Follow security patterns

---

## Tổng quan

Phase 3 tập trung vào việc migrate phần **xác thực (authentication)**, **phân quyền (authorization)**, và **quản lý ứng dụng (client management)** sang kiến trúc DDD. Đây là phase **quan trọng nhất về mặt bảo mật**, đòi hỏi sự cẩn thận cao và tuân thủ nghiêm ngặt các security best practices.

**🎯 Mục tiêu chính**: Xây dựng một hệ thống authentication và authorization **an toàn, bảo mật, và đáng tin cậy**, tuân thủ các security best practices và industry standards.

### Mục tiêu Phase 3

1. ✅ Tạo Domain Layer hoàn chỉnh cho IdentityAccess Context
2. ✅ Implement Application Layer với các Use Cases cho authentication, authorization, và token management
3. ✅ Migrate Infrastructure Layer với proper security measures
4. ✅ Tích hợp với OrganizationalStructure Context qua Domain Events
5. ✅ Maintain backward compatibility với Laravel Passport và existing authentication flows
6. ✅ Đảm bảo code quality cao với test coverage >= 95% (critical cho security code)
7. ✅ Implement proper RBAC (Role-Based Access Control) system

### Thời gian ước tính

**Tổng thời gian**: 17-20 ngày làm việc (136-160 giờ)

**Phân bổ**:
- Task 3.1: Domain Layer (4-5 ngày)
- Task 3.2: Application Layer (5-6 ngày)
- Task 3.3: Infrastructure Layer (7-8 ngày) - includes Data Migration Strategy và Enhanced Event Listeners - includes Data Migration Strategy

### ⚠️ SECURITY FIRST - CRITICAL REQUIREMENTS

**🚨 PHASE NÀY LÀ SECURITY-CRITICAL. BẤT KỲ LỖI BẢO MẬT NÀO ĐỀU CÓ THỂ DẪN ĐẾN BREACH NGHIÊM TRỌNG.**

#### Security Principles (Nguyên tắc Bảo mật)

1. **🔒 Defense in Depth (Bảo vệ nhiều lớp)**
   - Multiple layers of security controls
   - Fail secure, not fail open
   - Assume breach mentality

2. **🛡️ Principle of Least Privilege**
   - Users chỉ có permissions cần thiết
   - Services chỉ có access cần thiết
   - Default deny, explicit allow

3. **🔐 Secure by Default**
   - Strong defaults
   - Secure configurations
   - No insecure fallbacks

4. **✅ Fail Securely**
   - Generic error messages
   - No information leakage
   - Proper exception handling

5. **📝 Security by Design**
   - Security built-in, not bolted-on
   - Threat modeling
   - Security reviews mandatory

#### Security Requirements Checklist

**Authentication Security:**
- [ ] ✅ **NEVER log passwords** hoặc sensitive data (passwords, tokens, secrets)
- [ ] ✅ **ALWAYS hash passwords** với bcrypt (cost >= 10) hoặc argon2id
- [ ] ✅ **NEVER store plain passwords** anywhere (memory, logs, database, files)
- [ ] ✅ **Timing-safe password comparison** (use `hash_equals()` or `Hash::check()`)
- [ ] ✅ **Rate limiting** cho authentication endpoints (5 attempts per 15 minutes)
- [ ] ✅ **Account lockout** after multiple failed attempts (configurable)
- [ ] ✅ **Session management** secure (secure cookies, HttpOnly, SameSite)
- [ ] ✅ **CSRF protection** enabled cho tất cả state-changing operations
- [ ] ✅ **Generic error messages** (không reveal if user exists or not)
- [ ] ✅ **Password strength requirements** (min 8 chars, complexity rules)

**Authorization Security:**
- [ ] ✅ **RBAC properly implemented** với proper permission checks
- [ ] ✅ **Permission checks at multiple layers** (Domain, Application, Infrastructure)
- [ ] ✅ **Principle of least privilege** enforced
- [ ] ✅ **Role-based access control** với proper inheritance
- [ ] ✅ **Permission caching** với proper invalidation
- [ ] ✅ **Audit logging** cho all authorization decisions

**Token Security:**
- [ ] ✅ **Token expiration** (access tokens: 1 hour, refresh tokens: 30 days)
- [ ] ✅ **Refresh token rotation** implemented
- [ ] ✅ **Token revocation** works properly
- [ ] ✅ **Tokens stored securely** (HttpOnly cookies for web, secure storage for mobile)
- [ ] ✅ **Token validation** proper (signature, expiration, issuer)
- [ ] ✅ **Token scope validation** enforced
- [ ] ✅ **Token binding** (bind to IP/user agent if needed)

**Input Validation:**
- [ ] ✅ **Validate ALL inputs** rigorously (whitelist approach preferred)
- [ ] ✅ **Sanitize user input** before processing
- [ ] ✅ **SQL injection prevention** (use Eloquent/parameterized queries)
- [ ] ✅ **XSS prevention** (escape output, Content Security Policy)
- [ ] ✅ **Command injection prevention** (validate và sanitize)
- [ ] ✅ **Path traversal prevention** (validate file paths)
- [ ] ✅ **Type validation** (strict types, type hints)

**Data Protection:**
- [ ] ✅ **Encryption at rest** cho sensitive data (if required)
- [ ] ✅ **Encryption in transit** (HTTPS only, TLS 1.2+)
- [ ] ✅ **PII protection** (Personally Identifiable Information)
- [ ] ✅ **Data minimization** (only collect/store what's needed)
- [ ] ✅ **Secure deletion** của sensitive data
- [ ] ✅ **Backup encryption** (if backups contain sensitive data)

**Infrastructure Security:**
- [ ] ✅ **HTTPS only** cho production (HSTS enabled)
- [ ] ✅ **Security headers** configured (CSP, X-Frame-Options, etc.)
- [ ] ✅ **Dependency scanning** (check for vulnerabilities)
- [ ] ✅ **Regular security updates** (OS, PHP, Laravel, packages)
- [ ] ✅ **Environment variables** for secrets (never hardcode)
- [ ] ✅ **Secrets management** proper (use Laravel's config/encryption)

**Monitoring & Logging:**
- [ ] ✅ **Audit logging** cho security events (login, logout, permission changes, token issuance)
- [ ] ✅ **Security event monitoring** (failed logins, suspicious activity)
- [ ] ✅ **Log integrity** (prevent tampering)
- [ ] ✅ **Log retention** policy
- [ ] ✅ **Alerting** cho security incidents
- [ ] ✅ **No sensitive data in logs** (passwords, tokens, PII)

**Error Handling:**
- [ ] ✅ **Generic error messages** (không leak system info)
- [ ] ✅ **Proper exception handling** (catch, log, respond appropriately)
- [ ] ✅ **No stack traces** in production
- [ ] ✅ **Error logging** without sensitive data

**Testing:**
- [ ] ✅ **Security testing** included (SQL injection, XSS, CSRF, brute force)
- [ ] ✅ **Penetration testing** before production
- [ ] ✅ **Code review** với security focus
- [ ] ✅ **Dependency vulnerability scanning**
- [ ] ✅ **Security regression testing**

#### OWASP Top 10 (2021) Considerations

1. **A01:2021 – Broken Access Control**
   - ✅ Proper RBAC implementation
   - ✅ Permission checks at multiple layers
   - ✅ Token validation và scope checking

2. **A02:2021 – Cryptographic Failures**
   - ✅ Strong password hashing (bcrypt/argon2)
   - ✅ HTTPS only
   - ✅ Proper key management
   - ✅ No sensitive data in logs

3. **A03:2021 – Injection**
   - ✅ Parameterized queries (Eloquent)
   - ✅ Input validation
   - ✅ Output encoding
   - ✅ Command injection prevention

4. **A04:2021 – Insecure Design**
   - ✅ Security by design
   - ✅ Threat modeling
   - ✅ Secure defaults
   - ✅ Defense in depth

5. **A05:2021 – Security Misconfiguration**
   - ✅ Secure defaults
   - ✅ Proper configuration management
   - ✅ Security headers
   - ✅ Regular security audits

6. **A06:2021 – Vulnerable and Outdated Components**
   - ✅ Dependency scanning
   - ✅ Regular updates
   - ✅ Version pinning
   - ✅ Security advisories monitoring

7. **A07:2021 – Identification and Authentication Failures**
   - ✅ Strong password requirements
   - ✅ Rate limiting
   - ✅ Account lockout
   - ✅ Multi-factor authentication (if needed)

8. **A08:2021 – Software and Data Integrity Failures**
   - ✅ Dependency verification
   - ✅ Code signing (if applicable)
   - ✅ Secure CI/CD pipeline
   - ✅ Integrity checks

9. **A09:2021 – Security Logging and Monitoring Failures**
   - ✅ Comprehensive audit logging
   - ✅ Security event monitoring
   - ✅ Alerting
   - ✅ Log integrity

10. **A10:2021 – Server-Side Request Forgery (SSRF)**
    - ✅ Input validation
    - ✅ URL whitelisting
    - ✅ Network segmentation
    - ✅ Request validation

---

## Prerequisites (Điều kiện tiên quyết)

Trước khi bắt đầu Phase 3, đảm bảo:

### Setup Checklist

- [ ] ✅ Phase 1 và Phase 2 đã hoàn thành và merge vào main branch
- [ ] ✅ Đã review và approve architecture design trong `.ai-knowledge/commons/02-architecture.md`
- [ ] ✅ Đã setup development environment (PHP 8.3, Laravel 12, Composer)
- [ ] ✅ Đã tạo feature branch: `feat/migrate-ddd-phase3`
- [ ] ✅ Đã install Laravel Passport:
  ```bash
  composer require laravel/passport
  php artisan passport:install
  ```
- [ ] ✅ Đã hiểu rõ Laravel Passport OAuth2 flow
- [ ] ✅ Đã review existing authentication code (`AuthenticateController`, `AuthenticateSSOController`)
- [ ] ✅ Đã hiểu rõ database schema (users, roles, permissions, clients, oauth_access_tokens tables)
- [ ] ✅ Đã có database backup
- [ ] ✅ Đã setup CI/CD để chạy tests tự động
- [ ] ✅ Đã review security best practices document (nếu có)

### Environment Verification

**Steps để verify environment**:
1. [ ] Check Phase 1 và Phase 2 completion:
   ```bash
   # Verify SharedKernel exists
   ls -la app/SharedKernel/Domain/ValueObjects/
   # Verify OrganizationalStructure exists
   ls -la app/OrganizationalStructure/Domain/Aggregates/
   ```
2. [ ] Check Laravel Passport installation:
   ```bash
   php artisan passport:client --help
   # Should show Passport commands
   ```
3. [ ] Check database tables:
   ```bash
   php artisan db:show
   # Verify: users, roles, permissions, clients, oauth_access_tokens, oauth_clients
   ```
4. [ ] Check PHP version:
   ```bash
   php -v  # Should be PHP 8.3.x
   ```
5. [ ] Check Git branch:
   ```bash
   git branch  # Should be on feat/migrate-ddd-phase3
   ```
6. [ ] Run existing tests:
   ```bash
   php artisan test
   ```

**Verification**:
- [ ] ✅ Tất cả checks pass
- [ ] ✅ Phase 1 và Phase 2 components available
- [ ] ✅ Laravel Passport installed và configured
- [ ] ✅ Environment ready for development

---

## Workflow Chung cho Mỗi Task

### Standard Workflow

Mỗi task nên follow workflow sau:

1. **Planning** (15-20 phút)
   - [ ] Đọc task description và requirements
   - [ ] Review code examples từ `.ai-knowledge/commons/04-code-examples.md`
   - [ ] Review existing code để hiểu business logic và security requirements
   - [ ] Xác định security implications
   - [ ] Xác định dependencies và prerequisites
   - [ ] Estimate time

2. **Security Review** (10-15 phút) - **CRITICAL cho Phase 3**
   - [ ] Identify security risks
   - [ ] Plan security measures
   - [ ] Review OWASP Top 10 considerations
   - [ ] Plan input validation strategy
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
   - [ ] Unit tests cho Domain Layer
   - [ ] Feature tests cho Application Layer
   - [ ] Integration tests cho Infrastructure Layer
   - [ ] **Security tests**: SQL injection, XSS, CSRF, brute force protection
   - [ ] Manual security testing

6. **Code Quality** (15-20 phút)
   - [ ] Run Laravel Pint
   - [ ] Run PHPStan (nếu có)
   - [ ] Check test coverage (>= 95% cho security code)
   - [ ] Review code với security lens

7. **Documentation** (10-15 phút)
   - [ ] Update PHPDoc comments
   - [ ] Add inline comments cho complex logic
   - [ ] Document security considerations
   - [ ] Update architecture docs nếu cần

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

- ✅ **Input Validation**: Validate ALL inputs, sanitize user data
- ✅ **Password Security**: Never store plain passwords, use strong hashing
- ✅ **Token Security**: Proper expiration, refresh strategy, secure storage
- ✅ **Error Messages**: Generic error messages, không leak sensitive info
- ✅ **Rate Limiting**: Implement cho authentication endpoints
- ✅ **Audit Logging**: Log security events (login attempts, token issuance, etc.)
- ✅ **Principle of Least Privilege**: Users chỉ có permissions cần thiết
- ✅ **Defense in Depth**: Multiple layers of security
- ✅ **Secure by Default**: Fail secure, not fail open

---

## Task 3.1: Tạo Domain Layer

**Estimated Time**: 4-5 ngày (32-40 giờ)

**Mục tiêu**: Tạo toàn bộ Domain Layer cho IdentityAccess Context, bao gồm Aggregates, Value Objects, Events, Repository Interfaces, Domain Services Interfaces, và Domain Exceptions.

### Task 3.1.1: Tạo Value Objects

**Estimated Time**: 1.5 ngày (12 giờ)

#### Subtask 3.1.1.1: UserIdentityId Value Object

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo UserIdentityId Value Object - link với UserId từ OrganizationalStructure

**Steps**:
1. [ ] Tạo file structure:
   ```bash
   mkdir -p app/IdentityAccess/Domain/ValueObjects
   touch app/IdentityAccess/Domain/ValueObjects/UserIdentityId.php
   ```
2. [ ] Implement UserIdentityId class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\ValueObjects\Uuid;
   use InvalidArgumentException;
   
   /**
    * User Identity ID value object.
    * Links to UserId from OrganizationalStructure context.
    */
   final class UserIdentityId
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
       
       /**
        * Create from OrganizationalStructure UserId.
        */
       public static function fromOrganizationalStructureUserId(string $userId): self
       {
           return new self(Uuid::fromString($userId));
       }
       
       public function equals(UserIdentityId $other): bool
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
3. [ ] Write unit tests:
   - [ ] Test `fromString()` creates valid ID
   - [ ] Test `generate()` creates new ID
   - [ ] Test `fromOrganizationalStructureUserId()` creates ID from external context
   - [ ] Test `equals()` method
   - [ ] Test invalid UUID throws exception

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Sử dụng Uuid từ SharedKernel
- [ ] ✅ Có factory method để link với OrganizationalStructure UserId
- [ ] ✅ Type hints đầy đủ
- [ ] ✅ PHPDoc comments đầy đủ

**Security Review Checklist**:
- [ ] ✅ No sensitive data exposure
- [ ] ✅ Input validation (if applicable)
- [ ] ✅ Proper error handling
- [ ] ✅ No information leakage

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%
- [ ] Security tests pass

---

#### Subtask 3.1.1.2: Username Value Object

**Estimated Time**: 1.5 giờ

**Mục tiêu**: Tạo Username Value Object với validation rules

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/IdentityAccess/Domain/ValueObjects/Username.php
   ```
2. [ ] Implement Username class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Username value object.
    * Represents a unique username for authentication.
    */
   final class Username
   {
       private string $username;
       
       private function __construct(string $username)
       {
           $this->validate($username);
           $this->username = trim(strtolower($username));
       }
       
       public static function fromString(string $username): self
       {
           return new self($username);
       }
       
       public function equals(Username $other): bool
       {
           return $this->username === $other->username;
       }
       
       public function toString(): string
       {
           return $this->username;
       }
       
       public function __toString(): string
       {
           return $this->toString();
       }
       
       private function validate(string $username): void
       {
           $trimmed = trim($username);
           
           if (empty($trimmed)) {
               throw new InvalidArgumentException('Username cannot be empty');
           }
           
           if (strlen($trimmed) < 3) {
               throw new InvalidArgumentException('Username must be at least 3 characters');
           }
           
           if (strlen($trimmed) > 255) {
               throw new InvalidArgumentException('Username cannot exceed 255 characters');
           }
           
           // Allow alphanumeric, underscore, dot, hyphen
           if (!preg_match('/^[a-zA-Z0-9._-]+$/', $trimmed)) {
               throw new InvalidArgumentException('Username contains invalid characters');
           }
           
           // Security: Prevent SQL injection patterns
           if (preg_match('/(\b(OR|AND|UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b)/i', $trimmed)) {
               throw new InvalidArgumentException('Username contains invalid patterns');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   - [ ] Test valid username creation
   - [ ] Test empty username throws exception
   - [ ] Test too short username throws exception
   - [ ] Test too long username throws exception
   - [ ] Test invalid characters throw exception
   - [ ] Test SQL injection patterns are rejected
   - [ ] Test username is normalized (lowercase, trimmed)
   - [ ] Test `equals()` method

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Validation logic trong private method
- [ ] ✅ **Security: SQL injection prevention**
- [ ] ✅ Username normalized (lowercase, trimmed)
- [ ] ✅ Exception messages rõ ràng
- [ ] ✅ Length validation (3-255 characters)

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: SQL injection patterns rejected**
- [ ] ✅ Input validation comprehensive
- [ ] ✅ XSS prevention (no script tags)
- [ ] ✅ Command injection prevention
- [ ] ✅ Proper error messages (no information leakage)
- [ ] ✅ Input sanitization (trim, lowercase)
- [ ] ✅ Whitelist validation approach

**Security Tests Required**:
- [ ] ✅ Test SQL injection patterns are rejected
- [ ] ✅ Test XSS patterns are rejected
- [ ] ✅ Test command injection patterns are rejected
- [ ] ✅ Test input normalization works
- [ ] ✅ Test length validation works

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%
- [ ] Security tests pass
- [ ] Security review passed

---

#### Subtask 3.1.1.3: PasswordHash Value Object

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo PasswordHash Value Object - **CRITICAL SECURITY COMPONENT**

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/IdentityAccess/Domain/ValueObjects/PasswordHash.php
   ```
2. [ ] Implement PasswordHash class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   use Illuminate\Support\Facades\Hash;
   
   /**
    * Password hash value object.
    * CRITICAL: Never store plain passwords, only hashes.
    */
   final class PasswordHash
   {
       private string $hash;
       
       private function __construct(string $hash)
       {
           $this->validate($hash);
           $this->hash = $hash;
       }
       
       /**
        * Create password hash from plain text password.
        * This should be done via PasswordHasher service, not directly.
        */
       public static function fromPlainText(string $plainPassword, callable $hasher): self
       {
           if (empty(trim($plainPassword))) {
               throw new InvalidArgumentException('Password cannot be empty');
           }
           
           if (strlen($plainPassword) < 8) {
               throw new InvalidArgumentException('Password must be at least 8 characters');
           }
           
           $hash = $hasher($plainPassword);
           
           return new self($hash);
       }
       
       /**
        * Create from existing hash (e.g., from database).
        */
       public static function fromHash(string $hash): self
       {
           return new self($hash);
       }
       
       /**
        * Verify plain password against hash.
        */
       public function verify(string $plainPassword, callable $verifier): bool
       {
           return $verifier($plainPassword, $this->hash);
       }
       
       public function equals(PasswordHash $other): bool
       {
           return $this->hash === $other->hash;
       }
       
       public function toString(): string
       {
           return $this->hash;
       }
       
       /**
        * Never expose hash in string representation.
        */
       public function __toString(): string
       {
           return '[REDACTED]';
       }
       
       private function validate(string $hash): void
       {
           if (empty($hash)) {
               throw new InvalidArgumentException('Password hash cannot be empty');
           }
           
           // Verify it's a valid bcrypt/argon2 hash format
           if (!preg_match('/^\$2[ayb]\$|\$argon2/', $hash)) {
               throw new InvalidArgumentException('Invalid password hash format');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   - [ ] Test `fromPlainText()` creates hash
   - [ ] Test empty password throws exception
   - [ ] Test short password throws exception
   - [ ] Test `fromHash()` creates from existing hash
   - [ ] Test `verify()` returns true for correct password
   - [ ] Test `verify()` returns false for incorrect password
   - [ ] Test `__toString()` returns [REDACTED]
   - [ ] Test invalid hash format throws exception

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ **CRITICAL: Never stores plain password**
- [ ] ✅ **CRITICAL: `__toString()` returns [REDACTED]**
- [ ] ✅ Password validation (min length)
- [ ] ✅ Hash format validation
- [ ] ✅ Uses callable for hasher/verifier (dependency injection)

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Never stores plain password**
- [ ] ✅ **CRITICAL: `__toString()` returns [REDACTED]**
- [ ] ✅ **CRITICAL: Strong hashing algorithm (bcrypt cost >= 10 or argon2id)**
- [ ] ✅ **CRITICAL: Timing-safe password verification**
- [ ] ✅ Password validation (min 8 characters, complexity if required)
- [ ] ✅ Hash format validation
- [ ] ✅ Uses callable for hasher/verifier (dependency injection)
- [ ] ✅ No password in logs or exceptions
- [ ] ✅ No password in memory after use (if possible)

**Security Tests Required**:
- [ ] ✅ Test password hash is not plain text
- [ ] ✅ Test `__toString()` returns [REDACTED]
- [ ] ✅ Test hash format is valid (bcrypt/argon2)
- [ ] ✅ Test password verification is timing-safe
- [ ] ✅ Test empty password throws exception
- [ ] ✅ Test short password throws exception
- [ ] ✅ Test invalid hash format throws exception

**Security Notes**:
- ⚠️ **NEVER log passwords or hashes**
- ⚠️ **NEVER expose hash in string representation**
- ⚠️ **Always use strong hashing algorithm (bcrypt cost >= 10 or argon2id)**
- ⚠️ **Use timing-safe comparison (Hash::check uses constant-time comparison)**
- ⚠️ **Clear password from memory after hashing (if possible)**

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%
- [ ] Security tests pass
- [ ] Security review passed
- [ ] Password security audit passed

---

#### Subtask 3.1.1.4: ClientSecret Value Object

**Estimated Time**: 1.5 giờ

**Mục tiêu**: Tạo ClientSecret Value Object cho OAuth2 client secrets

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/IdentityAccess/Domain/ValueObjects/ClientSecret.php
   ```
2. [ ] Implement ClientSecret class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\ValueObjects;
   
   use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;
   
   /**
    * Client secret value object.
    * Represents OAuth2 client secret (should be hashed in storage).
    */
   final class ClientSecret
   {
       private string $secret;
       
       private function __construct(string $secret)
       {
           $this->validate($secret);
           $this->secret = $secret;
       }
       
       public static function fromString(string $secret): self
       {
           return new self($secret);
       }
       
       /**
        * Generate random client secret.
        */
       public static function generate(): self
       {
           return new self(bin2hex(random_bytes(32))); // 64 character hex string
       }
       
       public function equals(ClientSecret $other): bool
       {
           return hash_equals($this->secret, $other->secret); // Timing-safe comparison
       }
       
       public function toString(): string
       {
           return $this->secret;
       }
       
       /**
        * Never expose secret in string representation.
        */
       public function __toString(): string
       {
           return '[REDACTED]';
       }
       
       private function validate(string $secret): void
       {
           if (empty($secret)) {
               throw new InvalidArgumentException('Client secret cannot be empty');
           }
           
           if (strlen($secret) < 32) {
               throw new InvalidArgumentException('Client secret must be at least 32 characters');
           }
       }
   }
   ```
3. [ ] Write unit tests:
   - [ ] Test `fromString()` creates secret
   - [ ] Test `generate()` creates random secret
   - [ ] Test empty secret throws exception
   - [ ] Test short secret throws exception
   - [ ] Test `equals()` uses timing-safe comparison
   - [ ] Test `__toString()` returns [REDACTED]

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ **CRITICAL: `equals()` uses `hash_equals()` for timing-safe comparison**
- [ ] ✅ **CRITICAL: `__toString()` returns [REDACTED]**
- [ ] ✅ Minimum length validation (32 characters)
- [ ] ✅ Random generation uses cryptographically secure random

**Security Notes**:
- ⚠️ **Use `hash_equals()` for secret comparison (timing attack prevention)**
- ⚠️ **Never expose secret in logs or string representation**

**Verification**:
- [ ] Syntax check passes
- [ ] All unit tests pass
- [ ] Test coverage >= 95%
- [ ] Security review passed

---

#### Subtask 3.1.1.5: Commit Value Objects

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint:
   ```bash
   ./vendor/bin/pint app/IdentityAccess/Domain/ValueObjects/
   ```
2. [ ] Run all tests:
   ```bash
   php artisan test tests/Unit/IdentityAccess/Domain/ValueObjects/
   ```
3. [ ] Check coverage (>= 95%):
   ```bash
   php artisan test --coverage --min=95
   ```
4. [ ] Security review:
   - [ ] No passwords/secrets in code
   - [ ] No sensitive data in logs
   - [ ] Proper validation
   - [ ] Timing-safe comparisons
5. [ ] Stage và commit:
   ```bash
   git add app/IdentityAccess/Domain/ValueObjects/
   git add tests/Unit/IdentityAccess/Domain/ValueObjects/
   git commit -m "feat(IdentityAccess): add Value Objects

   - Add UserIdentityId value object linking to OrganizationalStructure
   - Add Username value object with SQL injection prevention
   - Add PasswordHash value object (CRITICAL: never stores plain password)
   - Add ClientSecret value object with timing-safe comparison
   - Add comprehensive unit tests with 95%+ coverage
   - Security: No sensitive data exposure"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Security review passed
- [ ] Code formatted correctly
- [ ] Commit successful

---

### Task 3.1.2: Tạo UserIdentity Aggregate

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tạo UserIdentity Aggregate Root để quản lý authentication credentials (không bao gồm profile)

#### Subtask 3.1.2.1: Setup và Structure

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Tạo folder structure:
   ```bash
   mkdir -p app/IdentityAccess/Domain/Aggregates
   touch app/IdentityAccess/Domain/Aggregates/UserIdentity.php
   ```
2. [ ] Review existing User model để hiểu authentication logic:
   ```bash
   # Review app/Models/User.php
   # Note: user_name, password, email (for login), is_change_password, is_only_login_ms
   ```
3. [ ] Xác định business rules:
   - [ ] User có thể login bằng username hoặc email
   - [ ] Password phải được hash
   - [ ] User có thể có flag `is_change_password`
   - [ ] User có thể có flag `is_only_login_ms` (chỉ login qua Microsoft)
   - [ ] UserIdentity links với UserId từ OrganizationalStructure

**Verification**:
- [ ] Folder structure created
- [ ] Existing code reviewed
- [ ] Business rules identified

---

#### Subtask 3.1.2.2: Implement UserIdentity Aggregate

**Estimated Time**: 5 giờ

**Mục tiêu**: Implement UserIdentity Aggregate với đầy đủ business logic và security

**Steps**:
1. [ ] Review code example từ `.ai-knowledge/commons/04-code-examples.md`
2. [ ] Implement UserIdentity class:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Aggregates;
   
   use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
   use App\IdentityAccess\Domain\ValueObjects\Username;
   use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\IdentityAccess\Domain\Events\UserIdentityWasCreated;
   use App\IdentityAccess\Domain\Events\PasswordWasChanged;
   use App\IdentityAccess\Domain\Events\UserWasAuthenticated;
   
   /**
    * User Identity aggregate root.
    * Manages authentication credentials (not profile).
    * Links to UserId from OrganizationalStructure context.
    */
   final class UserIdentity
   {
       private array $domainEvents = [];
       
       private UserIdentityId $id;
       private string $organizationalStructureUserId; // Link to OrganizationalStructure UserId
       private Username $username;
       private Email $email; // For login
       private PasswordHash $passwordHash;
       private bool $mustChangePassword;
       private bool $isOnlyMicrosoftLogin;
       private bool $isActive;
       
       private function __construct(
           UserIdentityId $id,
           string $organizationalStructureUserId,
           Username $username,
           Email $email,
           PasswordHash $passwordHash,
           bool $mustChangePassword = false,
           bool $isOnlyMicrosoftLogin = false,
           bool $isActive = true
       ) {
           $this->id = $id;
           $this->organizationalStructureUserId = $organizationalStructureUserId;
           $this->username = $username;
           $this->email = $email;
           $this->passwordHash = $passwordHash;
           $this->mustChangePassword = $mustChangePassword;
           $this->isOnlyMicrosoftLogin = $isOnlyMicrosoftLogin;
           $this->isActive = $isActive;
       }
       
       /**
        * Factory method to create a new user identity.
        */
       public static function create(
           UserIdentityId $id,
           string $organizationalStructureUserId,
           Username $username,
           Email $email,
           PasswordHash $passwordHash,
           bool $mustChangePassword = false,
           bool $isOnlyMicrosoftLogin = false
       ): self {
           $userIdentity = new self(
               $id,
               $organizationalStructureUserId,
               $username,
               $email,
               $passwordHash,
               $mustChangePassword,
               $isOnlyMicrosoftLogin,
               true
           );
           
           $userIdentity->recordEvent(new UserIdentityWasCreated(
               $id->toString(),
               $organizationalStructureUserId,
               $email->toString()
           ));
           
           return $userIdentity;
       }
       
       /**
        * Authenticate user with password.
        * Returns true if authentication successful.
        */
       public function authenticate(string $plainPassword, callable $passwordVerifier): bool
       {
           if (!$this->isActive) {
               return false;
           }
           
           if ($this->isOnlyMicrosoftLogin) {
               return false; // Cannot authenticate with password
           }
           
           $isValid = $this->passwordHash->verify($plainPassword, $passwordVerifier);
           
           if ($isValid) {
               $this->recordEvent(new UserWasAuthenticated(
                   $this->id->toString(),
                   $this->organizationalStructureUserId,
                   $this->email->toString()
               ));
           }
           
           return $isValid;
       }
       
       /**
        * Change password.
        */
       public function changePassword(PasswordHash $newPasswordHash): void
       {
           $this->passwordHash = $newPasswordHash;
           $this->mustChangePassword = false;
           
           $this->recordEvent(new PasswordWasChanged($this->id->toString()));
       }
       
       /**
        * Mark password as must be changed.
        */
       public function requirePasswordChange(): void
       {
           $this->mustChangePassword = true;
       }
       
       /**
        * Enable Microsoft-only login.
        */
       public function enableMicrosoftOnlyLogin(): void
       {
           $this->isOnlyMicrosoftLogin = true;
       }
       
       /**
        * Disable Microsoft-only login.
        */
       public function disableMicrosoftOnlyLogin(): void
       {
           $this->isOnlyMicrosoftLogin = false;
       }
       
       /**
        * Activate user identity.
       */
       public function activate(): void
       {
           $this->isActive = true;
       }
       
       /**
        * Deactivate user identity.
       */
       public function deactivate(): void
       {
           $this->isActive = false;
       }
       
       // Getters
       public function id(): UserIdentityId { return $this->id; }
       public function organizationalStructureUserId(): string { return $this->organizationalStructureUserId; }
       public function username(): Username { return $this->username; }
       public function email(): Email { return $this->email; }
       public function passwordHash(): PasswordHash { return $this->passwordHash; }
       public function mustChangePassword(): bool { return $this->mustChangePassword; }
       public function isOnlyMicrosoftLogin(): bool { return $this->isOnlyMicrosoftLogin; }
       public function isActive(): bool { return $this->isActive; }
       
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
   - [ ] Business methods: `authenticate()`, `changePassword()`, etc.
   - [ ] Domain events được record
   - [ ] Security: No plain password storage
   - [ ] Getters return Value Objects
   - [ ] No setters (immutability)

**Code Review Checklist**:
- [ ] ✅ Class là `final`
- [ ] ✅ Constructor là `private`
- [ ] ✅ Factory method `create()` với domain event
- [ ] ✅ **CRITICAL: `authenticate()` method với proper validation**
- [ ] ✅ **CRITICAL: No plain password storage**
- [ ] ✅ Business methods có logic validation
- [ ] ✅ Domain events được record đúng cách
- [ ] ✅ Getters return Value Objects
- [ ] ✅ No setters (immutability)
- [ ] ✅ PHPDoc comments đầy đủ

**Security Notes**:
- ⚠️ **Never store plain password**
- ⚠️ **Always verify password with timing-safe comparison**
- ⚠️ **Check `isActive` before authentication**
- ⚠️ **Check `isOnlyMicrosoftLogin` before password authentication**

**Verification**:
- [ ] Syntax check passes
- [ ] IDE không có errors
- [ ] Security review passed

---

#### Subtask 3.1.2.3: Write Unit Tests cho UserIdentity Aggregate

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo test file:
   ```bash
   mkdir -p tests/Unit/IdentityAccess/Domain/Aggregates
   touch tests/Unit/IdentityAccess/Domain/Aggregates/UserIdentityTest.php
   ```
2. [ ] Implement tests:
   - [ ] Test `create()` method creates user identity và records event
   - [ ] Test `authenticate()` returns true for correct password
   - [ ] Test `authenticate()` returns false for incorrect password
   - [ ] Test `authenticate()` returns false if user is inactive
   - [ ] Test `authenticate()` returns false if `isOnlyMicrosoftLogin` is true
   - [ ] Test `authenticate()` records `UserWasAuthenticated` event on success
   - [ ] Test `changePassword()` updates password và clears `mustChangePassword`
   - [ ] Test `changePassword()` records `PasswordWasChanged` event
   - [ ] Test `requirePasswordChange()` sets flag
   - [ ] Test `enableMicrosoftOnlyLogin()` sets flag
   - [ ] Test `disableMicrosoftOnlyLogin()` clears flag
   - [ ] Test `activate()` và `deactivate()` methods
   - [ ] Test `pullDomainEvents()` returns và clears events

**Test Example**:
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\SharedKernel\Domain\ValueObjects\Email;
use App\IdentityAccess\Domain\Events\UserIdentityWasCreated;
use App\IdentityAccess\Domain\Events\UserWasAuthenticated;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\TestCase;

class UserIdentityTest extends TestCase
{
    public function test_create_user_identity_records_event(): void
    {
        $id = UserIdentityId::generate();
        $username = Username::fromString('testuser');
        $email = Email::fromString('test@example.com');
        $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
        
        $userIdentity = UserIdentity::create(
            $id,
            'user-uuid-123',
            $username,
            $email,
            $passwordHash
        );
        
        $events = $userIdentity->pullDomainEvents();
        
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserIdentityWasCreated::class, $events[0]);
    }
    
    public function test_authenticate_returns_true_for_correct_password(): void
    {
        $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
        $userIdentity = $this->createUserIdentity($passwordHash);
        
        $result = $userIdentity->authenticate('password123', fn($p, $h) => Hash::check($p, $h));
        
        $this->assertTrue($result);
        
        $events = $userIdentity->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasAuthenticated::class, $events[0]);
    }
    
    public function test_authenticate_returns_false_for_incorrect_password(): void
    {
        $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
        $userIdentity = $this->createUserIdentity($passwordHash);
        
        $result = $userIdentity->authenticate('wrongpassword', fn($p, $h) => Hash::check($p, $h));
        
        $this->assertFalse($result);
        
        $events = $userIdentity->pullDomainEvents();
        $this->assertCount(0, $events); // No event for failed authentication
    }
    
    public function test_authenticate_returns_false_if_user_inactive(): void
    {
        $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
        $userIdentity = $this->createUserIdentity($passwordHash);
        $userIdentity->deactivate();
        
        $result = $userIdentity->authenticate('password123', fn($p, $h) => Hash::check($p, $h));
        
        $this->assertFalse($result);
    }
    
    public function test_authenticate_returns_false_if_microsoft_only_login(): void
    {
        $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
        $userIdentity = $this->createUserIdentity($passwordHash);
        $userIdentity->enableMicrosoftOnlyLogin();
        
        $result = $userIdentity->authenticate('password123', fn($p, $h) => Hash::check($p, $h));
        
        $this->assertFalse($result);
    }
    
    // Helper method
    private function createUserIdentity(PasswordHash $passwordHash): UserIdentity
    {
        return UserIdentity::create(
            UserIdentityId::generate(),
            'user-uuid-123',
            Username::fromString('testuser'),
            Email::fromString('test@example.com'),
            $passwordHash
        );
    }
}
```

**Verification**:
- [ ] All unit tests pass
- [ ] Test coverage >= 95%
- [ ] Security tests included

---

#### Subtask 3.1.2.4: Commit UserIdentity Aggregate

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run tests
3. [ ] Check coverage (>= 95%)
4. [ ] Security review
5. [ ] Commit:
   ```bash
   git add app/IdentityAccess/Domain/Aggregates/UserIdentity.php
   git add tests/Unit/IdentityAccess/Domain/Aggregates/UserIdentityTest.php
   git commit -m "feat(IdentityAccess): add UserIdentity aggregate

   - Add UserIdentity aggregate root for authentication credentials
   - Link to OrganizationalStructure UserId
   - Implement authenticate(), changePassword(), and status management methods
   - Add domain events: UserIdentityWasCreated, UserWasAuthenticated, PasswordWasChanged
   - Security: Never stores plain password, proper validation
   - Add comprehensive unit tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Security review passed
- [ ] Commit successful

---

### Task 3.1.3: Tạo Role và Permission Aggregates

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Tạo Role và Permission Aggregates cho RBAC system

#### Subtask 3.1.3.1: Tạo Role Aggregate

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/IdentityAccess/Domain/Aggregates/Role.php
   ```
2. [ ] Review existing Role model để hiểu business logic
3. [ ] Implement Role aggregate:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Aggregates;
   
   use App\IdentityAccess\Domain\ValueObjects\RoleId;
   use App\IdentityAccess\Domain\Events\RoleWasCreated;
   use App\IdentityAccess\Domain\Events\PermissionWasAssignedToRole;
   use App\IdentityAccess\Domain\Events\PermissionWasRemovedFromRole;
   
   /**
    * Role aggregate root.
    * Represents a role in RBAC system.
    */
   final class Role
   {
       private array $domainEvents = [];
       
       private RoleId $id;
       private string $name;
       private string $displayName;
       private ?string $description;
       private array $permissionIds; // Array of PermissionId strings
       
       private function __construct(
           RoleId $id,
           string $name,
           string $displayName,
           ?string $description = null
       ) {
           $this->id = $id;
           $this->name = trim($name);
           $this->displayName = trim($displayName);
           $this->description = $description ? trim($description) : null;
           $this->permissionIds = [];
       }
       
       public static function create(
           RoleId $id,
           string $name,
           string $displayName,
           ?string $description = null
       ): self {
           $role = new self($id, $name, $displayName, $description);
           
           $role->recordEvent(new RoleWasCreated(
               $id->toString(),
               $name,
               $displayName
           ));
           
           return $role;
       }
       
       /**
        * Assign permission to role.
        */
       public function assignPermission(string $permissionId): void
       {
           if (in_array($permissionId, $this->permissionIds, true)) {
               return; // Already assigned
           }
           
           $this->permissionIds[] = $permissionId;
           
           $this->recordEvent(new PermissionWasAssignedToRole(
               $this->id->toString(),
               $permissionId
           ));
       }
       
       /**
        * Remove permission from role.
        */
       public function removePermission(string $permissionId): void
       {
           $key = array_search($permissionId, $this->permissionIds, true);
           
           if ($key === false) {
               return; // Not assigned
           }
           
           unset($this->permissionIds[$key]);
           $this->permissionIds = array_values($this->permissionIds); // Re-index
           
           $this->recordEvent(new PermissionWasRemovedFromRole(
               $this->id->toString(),
               $permissionId
           ));
       }
       
       /**
        * Check if role has permission.
        */
       public function hasPermission(string $permissionId): bool
       {
           return in_array($permissionId, $this->permissionIds, true);
       }
       
       // Getters
       public function id(): RoleId { return $this->id; }
       public function name(): string { return $this->name; }
       public function displayName(): string { return $this->displayName; }
       public function description(): ?string { return $this->description; }
       
       /**
        * @return string[]
        */
       public function permissionIds(): array
       {
           return $this->permissionIds;
       }
       
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
- [ ] Role aggregate created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

#### Subtask 3.1.3.2: Tạo Permission Aggregate

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Tạo file:
   ```bash
   touch app/IdentityAccess/Domain/Aggregates/Permission.php
   ```
2. [ ] Review existing Permission model
3. [ ] Implement Permission aggregate (simpler than Role)
4. [ ] Write unit tests

**Verification**:
- [ ] Permission aggregate created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

#### Subtask 3.1.3.3: Tạo RoleId và PermissionId Value Objects

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Tạo RoleId Value Object
2. [ ] Tạo PermissionId Value Object
3. [ ] Write unit tests

**Verification**:
- [ ] Both Value Objects created
- [ ] Unit tests pass

---

### Task 3.1.4: Tạo Client Aggregate

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo Client Aggregate cho OAuth2 client management

**Steps**:
1. [ ] Review existing Client model (extends PassportClient)
2. [ ] Implement Client aggregate:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Aggregates;
   
   use App\IdentityAccess\Domain\ValueObjects\ClientId;
   use App\IdentityAccess\Domain\ValueObjects\ClientSecret;
   use App\IdentityAccess\Domain\Events\ClientWasRegistered;
   
   /**
    * Client aggregate root.
    * Represents OAuth2 client application.
    */
   final class Client
   {
       private array $domainEvents = [];
       
       private ClientId $id;
       private string $name;
       private ?string $description;
       private string $redirectUri;
       private ClientSecret $secret;
       private bool $isRevoked;
       
       // ... implementation
   }
   ```
3. [ ] Write unit tests

**Verification**:
- [ ] Client aggregate created
- [ ] Unit tests pass
- [ ] Coverage >= 95%

---

### Task 3.1.5: Tạo AccessToken Aggregate

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo AccessToken Aggregate để quản lý OAuth2 tokens

**Note**: AccessToken có thể là Entity thay vì Aggregate Root, tùy vào design. Nếu Laravel Passport quản lý tokens, có thể chỉ cần wrapper.

**Steps**:
1. [ ] Review Laravel Passport Token model
2. [ ] Decide: Aggregate Root hay Entity?
3. [ ] Implement AccessToken
4. [ ] Write unit tests

**Verification**:
- [ ] AccessToken created
- [ ] Unit tests pass

---

### Task 3.1.6: Tạo Domain Events

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo tất cả Domain Events cho IdentityAccess Context

**Steps**:
1. [ ] Tạo folder:
   ```bash
   mkdir -p app/IdentityAccess/Domain/Events
   ```
2. [ ] Implement events:
   - [ ] UserIdentityWasCreated
   - [ ] UserWasAuthenticated
   - [ ] PasswordWasChanged
   - [ ] TokenWasIssued
   - [ ] TokenWasRevoked
   - [ ] RoleWasCreated
   - [ ] PermissionWasAssignedToRole
   - [ ] PermissionWasRemovedFromRole
   - [ ] ClientWasRegistered

**Verification**:
- [ ] All events created
- [ ] Events are immutable

---

### Task 3.1.7: Tạo Repository Interfaces

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo Repository Interfaces cho tất cả Aggregates

**Steps**:
1. [ ] Tạo UserIdentityRepositoryInterface
2. [ ] Tạo RoleRepositoryInterface
3. [ ] Tạo PermissionRepositoryInterface
4. [ ] Tạo ClientRepositoryInterface
5. [ ] Tạo AccessTokenRepositoryInterface

**Verification**:
- [ ] All interfaces created
- [ ] Methods properly defined

---

### Task 3.1.8: Tạo Domain Services Interfaces

**Estimated Time**: 1 giờ

**Mục tiêu**: Tạo Domain Services Interfaces cho PasswordHasher và TokenGenerator

**Steps**:
1. [ ] Tạo PasswordHasherInterface:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Services;
   
   use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
   
   /**
    * Password hasher interface.
    */
   interface PasswordHasherInterface
   {
       public function hash(string $plainPassword): PasswordHash;
       
       public function verify(string $plainPassword, PasswordHash $hash): bool;
   }
   ```
2. [ ] Tạo TokenGeneratorInterface:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Services;
   
   use App\IdentityAccess\Domain\Aggregates\UserIdentity;
   use App\IdentityAccess\Domain\Aggregates\Client;
   
   /**
    * Token generator interface.
    */
   interface TokenGeneratorInterface
   {
       /**
        * Generate access token for user and client.
        */
       public function generateAccessToken(UserIdentity $userIdentity, Client $client, array $scopes = []): string;
       
       /**
        * Generate refresh token.
        */
       public function generateRefreshToken(UserIdentity $userIdentity, Client $client): string;
       
       /**
        * Validate token.
        */
       public function validateToken(string $token): ?array; // Returns user info or null
       
       /**
        * Revoke token.
        */
       public function revokeToken(string $token): void;
   }
   ```

**Verification**:
- [ ] Both interfaces created
- [ ] Methods properly defined

---

### Task 3.1.9: Tạo Domain Exceptions

**Estimated Time**: 30 phút

**Mục tiêu**: Tạo Domain Exceptions cho IdentityAccess Context

**Steps**:
1. [ ] Tạo InvalidCredentialsException:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Domain\Exceptions;
   
   use App\SharedKernel\Domain\Exceptions\DomainException;
   
   /**
    * Exception thrown when credentials are invalid.
    */
   final class InvalidCredentialsException extends DomainException
   {
       public static function invalid(): self
       {
           return new self('Invalid credentials');
       }
   }
   ```
2. [ ] Tạo UserIdentityNotFoundException
3. [ ] Tạo InsufficientPermissionException

**Verification**:
- [ ] All exceptions created
- [ ] Extend từ SharedKernel exceptions

---

### Task 3.1.10: Database Migrations (if needed)

**Estimated Time**: 2 giờ

**Mục tiêu**: Tạo database migrations nếu cần thay đổi database structure cho IdentityAccess

#### Subtask 3.1.10.1: Review Database Schema

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Review existing database schema:
   ```bash
   php artisan db:show
   ```
2. [ ] Check if current schema supports IdentityAccess structure:
   - [ ] Users table (for UserIdentity - có thể reuse hoặc separate table?)
   - [ ] Roles table structure
   - [ ] Permissions table structure
   - [ ] Role_permissions pivot table
   - [ ] Clients table (Laravel Passport)
   - [ ] OAuth tokens tables (Laravel Passport)
3. [ ] Decide on UserIdentity storage:
   - [ ] Option 1: Reuse users table (add columns for identity fields)
   - [ ] Option 2: Create separate user_identities table
   - [ ] **Recommendation**: Reuse users table với additional columns để minimize changes
4. [ ] Identify changes needed

**Verification**:
- [ ] Schema reviewed
- [ ] Storage strategy decided
- [ ] Changes identified

---

#### Subtask 3.1.10.2: Create Migrations (if needed)

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Create migrations nếu cần:
   ```bash
   # If separate user_identities table
   php artisan make:migration create_user_identities_table
   
   # If reuse users table
   php artisan make:migration add_identity_fields_to_users_table
   
   # For roles và permissions (if structure changes)
   php artisan make:migration update_roles_table_for_ddd
   php artisan make:migration update_permissions_table_for_ddd
   ```
2. [ ] Implement migrations
3. [ ] Test migrations
4. [ ] Commit

**Verification**:
- [ ] Migrations created (if needed)
- [ ] Migrations tested
- [ ] Committed

---

### Task 3.1.11: Commit Domain Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint trên toàn bộ Domain Layer
2. [ ] Run all unit tests
3. [ ] Check coverage (>= 95%)
4. [ ] Security review
5. [ ] Commit:
   ```bash
   git add app/IdentityAccess/Domain/
   git add database/migrations/ # if migrations created
   git add tests/Unit/IdentityAccess/Domain/
   git commit -m "feat(IdentityAccess): complete Domain Layer

   - Add UserIdentity aggregate for authentication credentials
   - Add Role and Permission aggregates for RBAC
   - Add Client aggregate for OAuth2 client management
   - Add AccessToken aggregate for token management
   - Add Value Objects: UserIdentityId, Username, PasswordHash, ClientSecret
   - Add Domain Events: UserIdentityWasCreated, UserWasAuthenticated, etc.
   - Add Repository Interfaces and Domain Services Interfaces
   - Add Domain Exceptions: InvalidCredentialsException, etc.
   - Add database migrations (if needed)
   - Security: Never stores plain passwords, proper validation
   - Add comprehensive unit tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Security review passed
- [ ] Migrations tested (if created)
- [ ] Commit successful

---

## Task 3.2: Tạo Application Layer

**Estimated Time**: 5-6 ngày (40-48 giờ)

**Mục tiêu**: Tạo Application Layer với DTOs và Use Cases cho authentication, authorization, và token management

### Task 3.2.1: Tạo DTOs

**Estimated Time**: 1 ngày (8 giờ)

**Steps**:
1. [ ] Tạo AuthenticateUserDTO
2. [ ] Tạo CreateRoleDTO
3. [ ] Tạo AssignPermissionToRoleDTO
4. [ ] Tạo RegisterClientDTO
5. [ ] Tạo IssueTokenDTO
6. [ ] Tạo ChangePasswordDTO

**Verification**:
- [ ] All DTOs created
- [ ] Properties properly typed

---

### Task 3.2.2: Tạo Authentication Use Cases

**Estimated Time**: 1.5 ngày (12 giờ)

#### Subtask 3.2.2.1: AuthenticateUserUseCase

**Estimated Time**: 4 giờ

**Mục tiêu**: Implement Use Case để authenticate user với username/password

**Steps**:
1. [ ] Tạo Use Case:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Application\UseCases;
   
   use App\IdentityAccess\Application\DTOs\AuthenticateUserDTO;
   use App\IdentityAccess\Domain\Aggregates\UserIdentity;
   use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
   use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
   use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
   use App\IdentityAccess\Domain\Exceptions\UserIdentityNotFoundException;
   use App\SharedKernel\Domain\ValueObjects\Email;
   use App\IdentityAccess\Domain\ValueObjects\Username;
   use App\SharedKernel\Infrastructure\EventDispatcher\EventDispatcherInterface;
   use Illuminate\Support\Facades\DB;
   use Illuminate\Support\Facades\RateLimiter;
   
   /**
    * Use case for authenticating a user.
    */
   final class AuthenticateUserUseCase
   {
       public function __construct(
           private readonly UserIdentityRepositoryInterface $userIdentityRepository,
           private readonly PasswordHasherInterface $passwordHasher,
           private readonly EventDispatcherInterface $eventDispatcher
       ) {
       }
       
       public function execute(AuthenticateUserDTO $dto): UserIdentity
       {
           // Rate limiting để prevent brute force attacks
           $key = 'authenticate:' . $dto->username;
           if (RateLimiter::tooManyAttempts($key, 5)) {
               throw InvalidCredentialsException::invalid();
           }
           
           return DB::transaction(function () use ($dto, $key) {
               // Find user identity by username or email
               $userIdentity = $this->findUserIdentity($dto->username);
               
               if ($userIdentity === null) {
                   RateLimiter::hit($key);
                   throw InvalidCredentialsException::invalid(); // Generic message
               }
               
               // Authenticate
               $isAuthenticated = $userIdentity->authenticate(
                   $dto->password,
                   fn($plain, $hash) => $this->passwordHasher->verify($plain, $hash)
               );
               
               if (!$isAuthenticated) {
                   RateLimiter::hit($key);
                   throw InvalidCredentialsException::invalid(); // Generic message
               }
               
               // Clear rate limiter on success
               RateLimiter::clear($key);
               
               // Save user identity (to persist domain events)
               $this->userIdentityRepository->save($userIdentity);
               
               // Dispatch domain events
               $events = $userIdentity->pullDomainEvents();
               foreach ($events as $event) {
                   $this->eventDispatcher->dispatch($event);
               }
               
               return $userIdentity;
           });
       }
       
       private function findUserIdentity(string $username): ?UserIdentity
       {
           // Try username first
           try {
               $usernameVO = Username::fromString($username);
               $userIdentity = $this->userIdentityRepository->findByUsername($usernameVO);
               if ($userIdentity !== null) {
                   return $userIdentity;
               }
           } catch (\Exception $e) {
               // Invalid username format, try email
           }
           
           // Try email
           try {
               $email = Email::fromString($username);
               $userIdentity = $this->userIdentityRepository->findByEmail($email);
               if ($userIdentity !== null) {
                   return $userIdentity;
               }
           } catch (\Exception $e) {
               // Invalid email format
           }
           
           return null;
       }
   }
   ```
2. [ ] Write feature tests:
   - [ ] Test successful authentication
   - [ ] Test invalid credentials throws exception
   - [ ] Test rate limiting after multiple failures
   - [ ] Test authentication by username
   - [ ] Test authentication by email
   - [ ] Test domain events are dispatched

**Security Review Checklist**:
- [ ] ✅ **CRITICAL: Rate limiting implemented** (5 attempts per 15 minutes)
- [ ] ✅ **CRITICAL: Generic error messages** (không reveal if user exists)
- [ ] ✅ **CRITICAL: Timing-safe password verification** (Hash::check)
- [ ] ✅ **CRITICAL: No password in logs**
- [ ] ✅ **CRITICAL: Account lockout after max attempts**
- [ ] ✅ Input validation (username/email format)
- [ ] ✅ Transaction rollback on error
- [ ] ✅ Domain events dispatched securely
- [ ] ✅ Audit logging for authentication attempts
- [ ] ✅ IP address và user agent logged (for security monitoring)

**Security Tests Required**:
- [ ] ✅ Test rate limiting blocks after max attempts
- [ ] ✅ Test generic error messages (no information leakage)
- [ ] ✅ Test timing-safe password verification
- [ ] ✅ Test account lockout works
- [ ] ✅ Test audit logging includes required fields
- [ ] ✅ Test no password in logs
- [ ] ✅ Test transaction rollback on error

**Security Notes**:
- ⚠️ **Rate limiting để prevent brute force attacks**
- ⚠️ **Generic error messages (không leak info về user existence)**
- ⚠️ **Timing-safe password verification (Hash::check uses constant-time comparison)**
- ⚠️ **Never log passwords or sensitive data**
- ⚠️ **Audit log all authentication attempts (success và failure)**
- ⚠️ **Clear rate limiter on successful authentication**

**Verification**:
- [ ] Use Case implemented
- [ ] Feature tests pass
- [ ] Security tests pass
- [ ] Security review passed
- [ ] Rate limiting tested và verified

---

#### Subtask 3.2.2.2: AuthenticateWithMicrosoftUseCase

**Estimated Time**: 3 giờ

**Mục tiêu**: Implement Use Case để authenticate user qua Microsoft Azure

**Steps**:
1. [ ] Implement AuthenticateWithMicrosoftUseCase
2. [ ] Integrate với Laravel Socialite
3. [ ] Handle user creation nếu chưa tồn tại
4. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

#### Subtask 3.2.2.3: CreateDefaultCredentialsUseCase

**Estimated Time**: 2 giờ

**Mục tiêu**: Implement Use Case để tạo default credentials khi UserWasCreated event từ OrganizationalStructure

**Steps**:
1. [ ] Implement CreateDefaultCredentialsUseCase
2. [ ] Listen to UserWasCreated event
3. [ ] Create UserIdentity với default password
4. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

### Task 3.2.3: Tạo Token Management Use Cases

**Estimated Time**: 1.5 ngày (12 giờ)

**Steps**:
1. [ ] Implement IssueAccessTokenUseCase
2. [ ] Implement ValidateTokenUseCase
3. [ ] Implement RevokeTokenUseCase
4. [ ] Write feature tests

**Verification**:
- [ ] All Use Cases implemented
- [ ] Tests pass

---

### Task 3.2.4: Tạo RBAC Use Cases

**Estimated Time**: 1 ngày (8 giờ)

**Steps**:
1. [ ] Implement CreateRoleUseCase
2. [ ] Implement AssignPermissionToRoleUseCase
3. [ ] Implement CheckPermissionUseCase
4. [ ] Write feature tests

**Verification**:
- [ ] All Use Cases implemented
- [ ] Tests pass

---

### Task 3.2.5: Tạo Client Management Use Cases

**Estimated Time**: 1 ngày (8 giờ)

**Steps**:
1. [ ] Implement RegisterClientUseCase
2. [ ] Write feature tests

**Verification**:
- [ ] Use Case implemented
- [ ] Tests pass

---

### Task 3.2.6: Commit Application Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all feature tests
3. [ ] Check coverage (>= 95%)
4. [ ] Security review
5. [ ] Commit:
   ```bash
   git add app/IdentityAccess/Application/
   git add tests/Feature/IdentityAccess/
   git commit -m "feat(IdentityAccess): add Application Layer

   - Add DTOs for all use cases
   - Add AuthenticateUserUseCase with rate limiting
   - Add AuthenticateWithMicrosoftUseCase
   - Add CreateDefaultCredentialsUseCase (event listener)
   - Add Token Management Use Cases
   - Add RBAC Use Cases
   - Add Client Management Use Cases
   - Security: Rate limiting, generic error messages, timing-safe verification
   - Add comprehensive feature tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Security review passed
- [ ] Commit successful

---

## Task 3.3: Tạo Infrastructure Layer

**Estimated Time**: 6-7 ngày (48-56 giờ)

**Mục tiêu**: Implement Infrastructure Layer với Repositories, Services, Controllers, Livewire components, Event Listeners, và Service Provider

### Task 3.3.1: Implement Repository Implementations

**Estimated Time**: 2 ngày (16 giờ)

**Steps**:
1. [ ] Implement EloquentUserIdentityRepository
2. [ ] Implement EloquentRoleRepository
3. [ ] Implement EloquentPermissionRepository
4. [ ] Implement EloquentClientRepository
5. [ ] Implement EloquentAccessTokenRepository
6. [ ] Write integration tests

**Verification**:
- [ ] All repositories implemented
- [ ] Integration tests pass

---

### Task 3.3.2: Implement Domain Services

**Estimated Time**: 1 ngày (8 giờ)

**Steps**:
1. [ ] Implement LaravelPasswordHasher:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Services;
   
   use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
   use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
   use Illuminate\Support\Facades\Hash;
   
   /**
    * Laravel password hasher implementation.
    */
   final class LaravelPasswordHasher implements PasswordHasherInterface
   {
       public function hash(string $plainPassword): PasswordHash
       {
           return PasswordHash::fromPlainText($plainPassword, fn($p) => Hash::make($p));
       }
       
       public function verify(string $plainPassword, PasswordHash $hash): bool
       {
           return Hash::check($plainPassword, $hash->toString());
       }
   }
   ```
2. [ ] Implement PassportTokenGenerator
3. [ ] Implement MicrosoftAuthService
4. [ ] Write integration tests

**Verification**:
- [ ] All services implemented
- [ ] Integration tests pass

---

### Task 3.3.3: Refactor Controllers

**Estimated Time**: 1.5 ngày (12 giờ)

**Steps**:
1. [ ] Refactor AuthenticateController để sử dụng Use Cases
2. [ ] Refactor RoleController
3. [ ] Refactor ClientController
4. [ ] Write feature tests

**Verification**:
- [ ] All controllers refactored
- [ ] Tests pass

---

### Task 3.3.4: Refactor Livewire Components

**Estimated Time**: 1 ngày (8 giờ)

**Steps**:
1. [ ] Refactor Role Livewire components
2. [ ] Refactor Client Livewire components
3. [ ] Write feature tests

**Verification**:
- [ ] All components refactored
- [ ] Tests pass

---

### Task 3.3.5: Tạo Event Listeners

**Estimated Time**: 0.5 ngày (4 giờ)

**Steps**:
1. [ ] Tạo CreateDefaultCredentialsWhenUserWasCreated listener:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\IdentityAccess\Infrastructure\Listeners;
   
   use App\OrganizationalStructure\Domain\Events\UserWasCreated;
   use App\IdentityAccess\Application\UseCases\CreateDefaultCredentialsUseCase;
   use App\IdentityAccess\Application\DTOs\CreateDefaultCredentialsDTO;
   
   /**
    * Listener for UserWasCreated event from OrganizationalStructure.
    */
   final class CreateDefaultCredentialsWhenUserWasCreated
   {
       public function __construct(
           private readonly CreateDefaultCredentialsUseCase $createDefaultCredentialsUseCase
       ) {
       }
       
       public function handle(UserWasCreated $event): void
       {
           $dto = new CreateDefaultCredentialsDTO(
               userId: $event->userId,
               email: $event->email,
               defaultPassword: 'password' // Should be configurable
           );
           
           $this->createDefaultCredentialsUseCase->execute($dto);
       }
   }
   ```
2. [ ] Register listener trong EventServiceProvider
3. [ ] Write integration tests

**Verification**:
- [ ] Listener created
- [ ] Registered correctly
- [ ] Tests pass

---

### Task 3.3.6: Tạo Service Provider

**Estimated Time**: 1 giờ

**Steps**:
1. [ ] Tạo IdentityAccessServiceProvider
2. [ ] Bind Repository Interfaces
3. [ ] Bind Domain Services Interfaces
4. [ ] Register trong config/app.php

**Verification**:
- [ ] Service Provider created
- [ ] Registered correctly
- [ ] Bindings work

---

### Task 3.3.7: Data Migration Strategy

**Estimated Time**: 1 ngày (8 giờ)

**Mục tiêu**: Plan và implement data migration cho IdentityAccess Context

#### Subtask 3.3.7.1: Plan Data Migration

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Review existing data:
   - [ ] Users data (for UserIdentity)
   - [ ] Roles data
   - [ ] Permissions data
   - [ ] Role_permissions relationships
   - [ ] Clients data (Laravel Passport)
2. [ ] Identify data mapping:
   - [ ] Old User model → New UserIdentity Aggregate
   - [ ] Old Role model → New Role Aggregate
   - [ ] Old Permission model → New Permission Aggregate
3. [ ] Plan migration strategy:
   - [ ] Migrate UserIdentity data
   - [ ] Migrate Roles và Permissions
   - [ ] Migrate relationships
   - [ ] Validation strategy
4. [ ] Document migration plan

**Verification**:
- [ ] Data reviewed
- [ ] Mapping identified
- [ ] Plan documented

---

#### Subtask 3.3.7.2: Create Data Migration Scripts

**Estimated Time**: 4 giờ

**Steps**:
1. [ ] Create migration command:
   ```bash
   php artisan make:command MigrateIdentityAccessToDDD
   ```
2. [ ] Implement migration logic:
   - [ ] Migrate UserIdentity data
   - [ ] Migrate Roles
   - [ ] Migrate Permissions
   - [ ] Migrate relationships
3. [ ] Add validation
4. [ ] Add rollback capability
5. [ ] Test với test data
6. [ ] Commit

**Verification**:
- [ ] Migration script created
- [ ] Tested
- [ ] Committed

---

#### Subtask 3.3.7.3: Execute và Validate

**Estimated Time**: 2 giờ

**Steps**:
1. [ ] Backup database
2. [ ] Run migration script
3. [ ] Validate migrated data
4. [ ] Fix issues
5. [ ] Document results

**Verification**:
- [ ] Migration executed
- [ ] Data validated
- [ ] Results documented

---

### Task 3.3.8: Commit Infrastructure Layer

**Estimated Time**: 30 phút

**Steps**:
1. [ ] Run Pint
2. [ ] Run all tests
3. [ ] Check coverage (>= 95%)
4. [ ] Security review
5. [ ] Commit:
   ```bash
   git add app/IdentityAccess/Infrastructure/
   git add config/app.php
   git add tests/
   git commit -m "feat(IdentityAccess): add Infrastructure Layer

   - Implement Eloquent repositories for all aggregates
   - Implement LaravelPasswordHasher and PassportTokenGenerator
   - Implement MicrosoftAuthService
   - Refactor Controllers to use Use Cases
   - Refactor Livewire components to use Use Cases
   - Add CreateDefaultCredentialsWhenUserWasCreated event listener
   - Add IdentityAccessServiceProvider
   - Add data migration scripts
   - Security: Proper password hashing, token management
   - Add comprehensive integration tests with 95%+ coverage"
   ```

**Verification**:
- [ ] All tests pass
- [ ] Coverage >= 95%
- [ ] Security review passed
- [ ] Commit successful

---

## Milestones và Progress Tracking

### Milestone 3.1: Domain Layer Complete

**Estimated Time**: 4-5 ngày

**Progress Checklist**:
- [ ] ✅ Value Objects created và tested (95%+ coverage)
- [ ] ✅ UserIdentity Aggregate created và tested
- [ ] ✅ Role và Permission Aggregates created và tested
- [ ] ✅ Client Aggregate created và tested
- [ ] ✅ AccessToken Aggregate created và tested
- [ ] ✅ Domain Events created
- [ ] ✅ Repository Interfaces created
- [ ] ✅ Domain Services Interfaces created
- [ ] ✅ Domain Exceptions created
- [ ] ✅ Security review passed

**Status**: ⏳ In Progress

---

### Milestone 3.2: Application Layer Complete

**Estimated Time**: 5-6 ngày

**Progress Checklist**:
- [ ] ✅ DTOs created
- [ ] ✅ Authentication Use Cases implemented và tested
- [ ] ✅ Token Management Use Cases implemented và tested
- [ ] ✅ RBAC Use Cases implemented và tested
- [ ] ✅ Client Management Use Cases implemented và tested
- [ ] ✅ Rate limiting implemented
- [ ] ✅ Security tests passed
- [ ] ✅ Feature tests coverage >= 95%

**Status**: ⏳ Pending

---

### Milestone 3.3: Infrastructure Layer Complete

**Estimated Time**: 7-8 ngày

**Progress Checklist**:
- [ ] ✅ Repository implementations created và tested
- [ ] ✅ Domain Services implementations created và tested
- [ ] ✅ Controllers refactored
- [ ] ✅ Livewire components refactored
- [ ] ✅ Event Listeners created và registered với error handling
- [ ] ✅ Retry strategy implemented cho event listeners
- [ ] ✅ Event processing monitoring setup
- [ ] ✅ Data migration scripts created và tested
- [ ] ✅ Service Provider created và registered
- [ ] ✅ Integration tests coverage >= 95%
- [ ] ✅ Security review passed

**Status**: ⏳ Pending

---

## Quick Reference Commands

### Development Commands

```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Unit/IdentityAccess/Domain/Aggregates/UserIdentityTest.php

# Run with coverage
php artisan test --coverage --min=95

# Code formatting
./vendor/bin/pint app/IdentityAccess/

# PHPStan (if configured)
./vendor/bin/phpstan analyse app/IdentityAccess/
```

### Security Testing Commands

```bash
# Test rate limiting
php artisan test --filter=RateLimitingTest

# Test authentication
php artisan test --filter=AuthenticationTest

# Test password security
php artisan test --filter=PasswordSecurityTest
```

### Git Commands

```bash
# Create feature branch
git checkout -b feat/migrate-ddd-phase3

# Stage files
git add app/IdentityAccess/

# Commit
git commit -m "feat(IdentityAccess): <description>"

# Push
git push origin feat/migrate-ddd-phase3
```

---

## Troubleshooting Guide

### Issue: Password verification fails even with correct password

**Symptoms**: `authenticate()` returns false với correct password

**Root Cause**: Password hash format mismatch hoặc hasher implementation sai

**Solution**:
1. Check password hash format trong database
2. Verify PasswordHasher implementation
3. Check Hash::check() usage
4. Verify password was hashed correctly khi tạo

**Prevention**: Write comprehensive tests cho password hashing và verification

---

### Issue: Rate limiting không hoạt động

**Symptoms**: Multiple failed login attempts không trigger rate limit

**Root Cause**: RateLimiter configuration hoặc cache driver issue

**Solution**:
1. Check cache driver configuration
2. Verify RateLimiter::hit() và tooManyAttempts() calls
3. Check cache key format
4. Test với different cache drivers

**Prevention**: Write integration tests cho rate limiting

---

### Issue: Domain Events không được dispatch

**Symptoms**: Events được record nhưng không được dispatch

**Root Cause**: EventDispatcher không được inject đúng cách hoặc outbox pattern chưa setup

**Solution**:
1. Check Service Provider bindings
2. Verify EventDispatcherInterface binding
3. Check outbox pattern implementation từ Phase 1
4. Verify event listeners registration

**Prevention**: Write integration tests để verify event dispatch

---

### Issue: Microsoft authentication không hoạt động

**Symptoms**: Microsoft login fails hoặc không tạo user

**Root Cause**: Socialite configuration hoặc event listener issue

**Solution**:
1. Check Socialite Azure configuration
2. Verify CreateDefaultCredentialsUseCase
3. Check event listener registration
4. Verify user creation logic

**Prevention**: Write feature tests cho Microsoft authentication flow

---

## Security Patterns và Anti-Patterns

### ✅ Security Patterns (Nên làm)

#### 1. Password Hashing Pattern
```php
// ✅ GOOD: Use strong hashing với proper cost
$hash = Hash::make($password); // Uses bcrypt with cost 10

// ✅ GOOD: Verify với timing-safe comparison
$isValid = Hash::check($plainPassword, $storedHash);

// ✅ GOOD: Never expose hash
public function __toString(): string
{
    return '[REDACTED]';
}
```

#### 2. Rate Limiting Pattern
```php
// ✅ GOOD: Rate limit authentication attempts
$key = 'authenticate:' . $username;
if (RateLimiter::tooManyAttempts($key, 5)) {
    throw InvalidCredentialsException::invalid();
}

// Clear on success
RateLimiter::clear($key);
```

#### 3. Generic Error Messages Pattern
```php
// ✅ GOOD: Generic error message
throw InvalidCredentialsException::invalid(); // "Invalid credentials"

// ❌ BAD: Specific error message
throw new Exception("User not found"); // Leaks information
```

#### 4. Input Validation Pattern
```php
// ✅ GOOD: Whitelist validation
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
    throw new InvalidArgumentException('Invalid username');
}

// ✅ GOOD: Type validation
public function fromString(string $value): self
{
    // Validate type và format
}
```

#### 5. Token Security Pattern
```php
// ✅ GOOD: Token với expiration và scope
$token = $this->tokenGenerator->generateAccessToken(
    $userIdentity,
    $client,
    scopes: ['read', 'write'] // Explicit scopes
);

// ✅ GOOD: Validate token properly
$userInfo = $this->tokenGenerator->validateToken($token);
if ($userInfo === null || !in_array('write', $userInfo['scopes'])) {
    throw InsufficientPermissionException::forScope('write');
}
```

#### 6. Audit Logging Pattern
```php
// ✅ GOOD: Log security events
Log::channel('security')->info('User authenticated', [
    'user_id' => $userIdentity->id()->toString(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now()->toIso8601String(),
    // NO passwords, tokens, or sensitive data
]);
```

### ❌ Security Anti-Patterns (Không nên làm)

#### 1. Plain Password Storage
```php
// ❌ BAD: Never store plain password
$this->password = $plainPassword;

// ❌ BAD: Never log password
Log::info('User login', ['password' => $password]);

// ❌ BAD: Never return password in response
return ['password' => $user->password];
```

#### 2. Weak Password Hashing
```php
// ❌ BAD: MD5 is insecure
$hash = md5($password);

// ❌ BAD: SHA1 is insecure
$hash = sha1($password);

// ❌ BAD: Low cost bcrypt
$hash = Hash::make($password, ['rounds' => 4]); // Too low
```

#### 3. Information Leakage
```php
// ❌ BAD: Reveal if user exists
if (!$user) {
    throw new Exception("User with email {$email} not found");
}

// ❌ BAD: Reveal system info
catch (\Exception $e) {
    return response()->json(['error' => $e->getMessage()]); // Stack trace
}
```

#### 4. SQL Injection
```php
// ❌ BAD: Raw SQL với user input
DB::select("SELECT * FROM users WHERE email = '{$email}'");

// ✅ GOOD: Parameterized query
User::where('email', $email)->first();
```

#### 5. XSS Vulnerability
```php
// ❌ BAD: Output user input without escaping
echo $userInput;

// ✅ GOOD: Escape output
echo e($userInput); // Laravel helper
// or
{{ $userInput }} // Blade auto-escapes
```

#### 6. Timing Attacks
```php
// ❌ BAD: String comparison for secrets
if ($providedSecret === $storedSecret) {
    // Vulnerable to timing attack
}

// ✅ GOOD: Timing-safe comparison
if (hash_equals($storedSecret, $providedSecret)) {
    // Safe from timing attacks
}
```

#### 7. Weak Token Validation
```php
// ❌ BAD: No expiration check
$token = $request->bearerToken();
$user = User::where('api_token', $token)->first();

// ✅ GOOD: Proper token validation
$userInfo = $this->tokenGenerator->validateToken($token);
if ($userInfo === null || $userInfo['expires_at'] < now()) {
    throw InvalidTokenException::expired();
}
```

---

## Security Testing Guide

### Unit Tests cho Security

#### Password Security Tests
```php
public function test_password_hash_is_not_plain_text(): void
{
    $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
    
    $hashString = $passwordHash->toString();
    
    // Verify it's a bcrypt hash
    $this->assertStringStartsWith('$2y$', $hashString);
    $this->assertNotEquals('password123', $hashString);
}

public function test_password_hash_to_string_returns_redacted(): void
{
    $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
    
    $this->assertEquals('[REDACTED]', (string) $passwordHash);
}
```

#### Rate Limiting Tests
```php
public function test_rate_limiting_blocks_after_max_attempts(): void
{
    $dto = new AuthenticateUserDTO('testuser', 'wrongpassword');
    
        // Try 5 times
        for ($i = 0; $i < 5; $i++) {
            try {
                $this->authenticateUserUseCase->execute($dto);
            } catch (InvalidCredentialsException $e) {
                // Expected
            }
        }
        
        // 6th attempt should be blocked
        $this->expectException(InvalidCredentialsException::class);
        $this->authenticateUserUseCase->execute($dto);
}
```

#### SQL Injection Tests
```php
public function test_username_rejects_sql_injection_patterns(): void
{
    $this->expectException(InvalidArgumentException::class);
    
    Username::fromString("admin' OR '1'='1");
}

public function test_find_by_username_prevents_sql_injection(): void
{
    // This should not execute SQL injection
    $maliciousInput = "admin' OR '1'='1";
    
    try {
        $username = Username::fromString($maliciousInput);
        // Should throw exception before reaching repository
    } catch (InvalidArgumentException $e) {
        $this->assertTrue(true); // Expected
    }
}
```

#### XSS Prevention Tests
```php
public function test_username_rejects_xss_patterns(): void
{
    $this->expectException(InvalidArgumentException::class);
    
    Username::fromString('<script>alert("xss")</script>');
}
```

#### Timing Attack Tests
```php
public function test_password_verification_is_timing_safe(): void
{
    $passwordHash = PasswordHash::fromPlainText('password123', fn($p) => Hash::make($p));
    
    $start = microtime(true);
    $passwordHash->verify('wrongpassword', fn($p, $h) => Hash::check($p, $h));
    $wrongTime = microtime(true) - $start;
    
    $start = microtime(true);
    $passwordHash->verify('password123', fn($p, $h) => Hash::check($p, $h));
    $correctTime = microtime(true) - $start;
    
    // Timing should be similar (Hash::check uses timing-safe comparison)
    $this->assertLessThan(0.01, abs($wrongTime - $correctTime));
}
```

### Integration Tests cho Security

#### Authentication Flow Tests
```php
public function test_authentication_flow_with_rate_limiting(): void
{
    // Test successful authentication
    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);
    
    $response->assertStatus(200);
    $this->assertNotNull($response->json('token'));
    
    // Test rate limiting
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/auth/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);
    }
    
    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);
    
    $response->assertStatus(429); // Too Many Requests
}
```

#### Token Security Tests
```php
public function test_expired_token_is_rejected(): void
{
    $token = $this->generateExpiredToken();
    
    $response = $this->withToken($token)->getJson('/api/user/profile');
    
    $response->assertStatus(401);
}

public function test_token_without_required_scope_is_rejected(): void
{
    $token = $this->generateTokenWithScopes(['read']);
    
    $response = $this->withToken($token)->postJson('/api/user/update', []);
    
    $response->assertStatus(403); // Forbidden
}
```

### Security Penetration Testing Checklist

- [ ] ✅ **Brute Force Protection**: Test với automated tools
- [ ] ✅ **SQL Injection**: Test với SQL injection payloads
- [ ] ✅ **XSS**: Test với XSS payloads
- [ ] ✅ **CSRF**: Test CSRF protection
- [ ] ✅ **Session Fixation**: Test session security
- [ ] ✅ **Token Security**: Test token manipulation
- [ ] ✅ **Authorization Bypass**: Test permission checks
- [ ] ✅ **Information Disclosure**: Test error messages
- [ ] ✅ **Rate Limiting**: Test rate limit bypass attempts
- [ ] ✅ **Input Validation**: Test với various malicious inputs

---

## Security Review Checklist

### Pre-Commit Security Review

Trước khi commit bất kỳ code nào, verify:

#### Code Review
- [ ] ✅ No passwords/secrets in code
- [ ] ✅ No sensitive data in logs
- [ ] ✅ Input validation implemented
- [ ] ✅ Output encoding/escaping
- [ ] ✅ SQL injection prevention
- [ ] ✅ XSS prevention
- [ ] ✅ CSRF protection
- [ ] ✅ Proper error handling
- [ ] ✅ Generic error messages
- [ ] ✅ Security headers configured

#### Authentication Review
- [ ] ✅ Password hashing strong (bcrypt cost >= 10)
- [ ] ✅ Password never stored plain
- [ ] ✅ Password never logged
- [ ] ✅ Rate limiting implemented
- [ ] ✅ Account lockout implemented
- [ ] ✅ Session management secure
- [ ] ✅ Timing-safe password comparison

#### Authorization Review
- [ ] ✅ Permission checks at multiple layers
- [ ] ✅ Principle of least privilege
- [ ] ✅ RBAC properly implemented
- [ ] ✅ Token scope validation
- [ ] ✅ Authorization bypass prevention

#### Token Review
- [ ] ✅ Token expiration implemented
- [ ] ✅ Token validation proper
- [ ] ✅ Token revocation works
- [ ] ✅ Token stored securely
- [ ] ✅ Refresh token rotation

#### Data Protection Review
- [ ] ✅ Encryption at rest (if needed)
- [ ] ✅ Encryption in transit (HTTPS)
- [ ] ✅ PII protection
- [ ] ✅ Secure deletion
- [ ] ✅ Backup encryption

#### Testing Review
- [ ] ✅ Security tests included
- [ ] ✅ Test coverage >= 95%
- [ ] ✅ Penetration testing done
- [ ] ✅ Security regression tests

### Security Audit Checklist (Before Production)

- [ ] ✅ **Code Review**: Security-focused code review completed
- [ ] ✅ **Dependency Scan**: No known vulnerabilities
- [ ] ✅ **Penetration Test**: Professional pen test completed
- [ ] ✅ **Security Headers**: All security headers configured
- [ ] ✅ **HTTPS**: HTTPS only, HSTS enabled
- [ ] ✅ **Monitoring**: Security monitoring configured
- [ ] ✅ **Incident Response**: Incident response plan ready
- [ ] ✅ **Backup Security**: Backups encrypted và secure
- [ ] ✅ **Access Control**: Proper access controls in place
- [ ] ✅ **Documentation**: Security documentation complete

---

## Security Best Practices Checklist

### Authentication

- [ ] ✅ Passwords never stored in plain text
- [ ] ✅ Strong password hashing (bcrypt cost >= 10 or argon2id)
- [ ] ✅ Rate limiting implemented (5 attempts per 15 minutes)
- [ ] ✅ Account lockout after multiple failures
- [ ] ✅ Generic error messages (không leak info)
- [ ] ✅ Timing-safe password comparison (Hash::check or hash_equals)
- [ ] ✅ Session management secure (secure cookies, HttpOnly, SameSite)
- [ ] ✅ CSRF protection enabled
- [ ] ✅ Password strength requirements enforced
- [ ] ✅ Multi-factor authentication (if required)

### Token Management

- [ ] ✅ Tokens have expiration (access: 1 hour, refresh: 30 days)
- [ ] ✅ Refresh token rotation implemented
- [ ] ✅ Token revocation works properly
- [ ] ✅ Tokens stored securely (HttpOnly cookies or secure storage)
- [ ] ✅ Token validation proper (signature, expiration, issuer, scope)
- [ ] ✅ Token scope validation enforced
- [ ] ✅ Token binding (IP/user agent if needed)

### Authorization

- [ ] ✅ Principle of least privilege enforced
- [ ] ✅ RBAC properly implemented
- [ ] ✅ Permission checks at multiple layers (Domain, Application, Infrastructure)
- [ ] ✅ Audit logging for all authorization decisions
- [ ] ✅ Role-based access control với proper inheritance
- [ ] ✅ Permission caching với proper invalidation

### Input Validation & Output Encoding

- [ ] ✅ Validate ALL inputs rigorously (whitelist approach preferred)
- [ ] ✅ Sanitize user input before processing
- [ ] ✅ SQL injection prevention (Eloquent/parameterized queries)
- [ ] ✅ XSS prevention (escape output, Content Security Policy)
- [ ] ✅ Command injection prevention
- [ ] ✅ Path traversal prevention
- [ ] ✅ Type validation (strict types, type hints)
- [ ] ✅ Output encoding/escaping

### Data Protection

- [ ] ✅ Encryption at rest for sensitive data (if required)
- [ ] ✅ Encryption in transit (HTTPS only, TLS 1.2+)
- [ ] ✅ PII protection (Personally Identifiable Information)
- [ ] ✅ Data minimization (only collect/store what's needed)
- [ ] ✅ Secure deletion of sensitive data
- [ ] ✅ Backup encryption (if backups contain sensitive data)

### Infrastructure Security

- [ ] ✅ HTTPS only for production (HSTS enabled)
- [ ] ✅ Security headers configured (CSP, X-Frame-Options, X-Content-Type-Options, etc.)
- [ ] ✅ Dependency scanning (check for vulnerabilities)
- [ ] ✅ Regular security updates (OS, PHP, Laravel, packages)
- [ ] ✅ Environment variables for secrets (never hardcode)
- [ ] ✅ Secrets management proper (use Laravel's config/encryption)
- [ ] ✅ Network security (firewall, network segmentation)

### Monitoring & Logging

- [ ] ✅ Audit logging for security events (login, logout, permission changes, token issuance)
- [ ] ✅ Security event monitoring (failed logins, suspicious activity)
- [ ] ✅ Log integrity (prevent tampering)
- [ ] ✅ Log retention policy
- [ ] ✅ Alerting for security incidents
- [ ] ✅ No sensitive data in logs (passwords, tokens, PII)
- [ ] ✅ Log analysis và correlation

### Error Handling

- [ ] ✅ Generic error messages (không leak system info)
- [ ] ✅ Proper exception handling (catch, log, respond appropriately)
- [ ] ✅ No stack traces in production
- [ ] ✅ Error logging without sensitive data
- [ ] ✅ Proper HTTP status codes

### Testing & Code Quality

- [ ] ✅ Security testing included (SQL injection, XSS, CSRF, brute force)
- [ ] ✅ Penetration testing before production
- [ ] ✅ Code review với security focus
- [ ] ✅ Dependency vulnerability scanning
- [ ] ✅ Security regression testing
- [ ] ✅ Test coverage >= 95% for security-critical code

---

## Common Questions & Answers

### Q: Có nên store plain password tạm thời trong memory không?

**A**: **KHÔNG BAO GIỜ**. Ngay cả trong memory cũng không nên. Luôn hash password ngay lập tức và không bao giờ store plain password ở bất kỳ đâu.

**Recommendation**: Hash password ngay khi nhận từ user input.

---

### Q: Làm sao handle password reset?

**A**: 
1. Generate secure reset token
2. Store hashed token trong database với expiration
3. Send reset link qua email
4. Validate token khi user clicks link
5. Allow password change với valid token

**Recommendation**: Implement trong Phase 4 hoặc separate feature.

---

### Q: Rate limiting bao nhiêu attempts là hợp lý?

**A**: 
- **5 attempts** trong 15 phút cho authentication
- **10 attempts** trong 1 giờ cho password reset
- Adjust dựa trên business requirements

**Recommendation**: Configurable trong config file.

---

### Q: Có cần implement 2FA (Two-Factor Authentication)?

**A**: Tùy vào business requirements. Nếu cần:
1. Add TOTP secret to UserIdentity
2. Implement TOTP generation và verification
3. Add 2FA flag
4. Require 2FA for sensitive operations

**Recommendation**: Implement trong Phase 4 hoặc separate feature nếu cần.

---

### Q: Làm sao test security code?

**A**:
1. **Unit tests**: Test password hashing, validation, etc.
2. **Integration tests**: Test authentication flow, rate limiting
3. **Security tests**: SQL injection, XSS, brute force protection
4. **Manual testing**: Penetration testing, security audit

**Recommendation**: Include security tests trong test suite.

---

## Summary

Phase 3 là phase **quan trọng nhất về mặt bảo mật**, đòi hỏi sự cẩn thận cao và tuân thủ nghiêm ngặt các security best practices. Cần:

1. ✅ **Security First**: Luôn nghĩ về security implications
2. ✅ **Never Store Plain Passwords**: Hash ngay lập tức
3. ✅ **Rate Limiting**: Prevent brute force attacks
4. ✅ **Generic Error Messages**: Không leak sensitive info
5. ✅ **Timing-Safe Comparisons**: Prevent timing attacks
6. ✅ **Comprehensive Testing**: Unit, Feature, Integration, Security tests
7. ✅ **Audit Logging**: Log security events
8. ✅ **Follow Workflow**: Planning → Security Review → Implementation → Testing → Review

**Estimated Total Time**: 15-18 ngày làm việc (120-144 giờ)

**Success Criteria**:
- ✅ Domain Layer hoàn chỉnh với test coverage >= 95%
- ✅ Application Layer hoàn chỉnh với test coverage >= 95%
- ✅ Infrastructure Layer hoàn chỉnh với test coverage >= 95%
- ✅ Security best practices implemented
- ✅ Rate limiting implemented
- ✅ Backward compatibility maintained với Laravel Passport
- ✅ Event-driven integration với OrganizationalStructure Context
- ✅ Code quality standards met
- ✅ Security review passed
- ✅ Documentation complete

**⚠️ CRITICAL**: Security code phải được review bởi security expert trước khi merge vào main branch.

---

## Security Incident Response Plan

### Incident Classification

**Severity Levels:**
1. **CRITICAL**: Active breach, data exposure, system compromise
2. **HIGH**: Potential breach, suspicious activity, vulnerability discovered
3. **MEDIUM**: Security misconfiguration, failed attack attempts
4. **LOW**: Security improvements, best practice violations

### Incident Response Steps

#### 1. Detection
- [ ] Monitor security logs và alerts
- [ ] Review failed authentication attempts
- [ ] Check for unusual access patterns
- [ ] Monitor token usage patterns

#### 2. Containment
- [ ] **Immediate**: Disable affected accounts/tokens
- [ ] **Short-term**: Block suspicious IPs
- [ ] **Long-term**: Review và update security controls

#### 3. Investigation
- [ ] Review logs và audit trails
- [ ] Identify scope of incident
- [ ] Document findings
- [ ] Preserve evidence

#### 4. Eradication
- [ ] Remove threat
- [ ] Patch vulnerabilities
- [ ] Update security controls
- [ ] Revoke compromised credentials

#### 5. Recovery
- [ ] Restore services
- [ ] Verify system integrity
- [ ] Monitor for recurrence
- [ ] Update monitoring rules

#### 6. Post-Incident
- [ ] Document incident report
- [ ] Conduct post-mortem
- [ ] Update security procedures
- [ ] Train team on lessons learned

### Security Contacts

- **Security Team**: security@example.com
- **On-Call Engineer**: [Phone]
- **Management**: [Contact]

### Escalation Path

1. **Level 1**: Developer detects issue → Report to team lead
2. **Level 2**: Team lead → Security team
3. **Level 3**: Security team → Management
4. **Level 4**: Management → External security experts (if needed)

---

## Security Configuration Checklist

### Laravel Security Configuration

#### config/app.php
```php
// ✅ GOOD: Secure session configuration
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true,
'same_site' => 'strict',
```

#### config/session.php
```php
// ✅ GOOD: Secure session settings
'driver' => env('SESSION_DRIVER', 'database'),
'lifetime' => 120, // 2 hours
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'strict',
```

#### config/cors.php
```php
// ✅ GOOD: Restrictive CORS policy
'allowed_origins' => [
    env('FRONTEND_URL', 'https://example.com'),
],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
'allowed_headers' => ['Content-Type', 'Authorization'],
'supports_credentials' => true,
'max_age' => 3600,
```

### Security Headers (Middleware)

```php
// ✅ GOOD: Security headers middleware
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    return $response
        ->header('X-Content-Type-Options', 'nosniff')
        ->header('X-Frame-Options', 'DENY')
        ->header('X-XSS-Protection', '1; mode=block')
        ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
        ->header('Content-Security-Policy', "default-src 'self'");
}
```

### Rate Limiting Configuration

```php
// ✅ GOOD: Rate limiting configuration
Route::middleware(['throttle:5,15'])->group(function () {
    Route::post('/login', [AuthenticateController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
});
```

### Password Requirements

```php
// ✅ GOOD: Password validation rules
'password' => [
    'required',
    'string',
    'min:8',
    'regex:/[a-z]/',      // At least one lowercase
    'regex:/[A-Z]/',      // At least one uppercase
    'regex:/[0-9]/',      // At least one digit
    'regex:/[@$!%*#?&]/', // At least one special character
],
```

---

## Security Monitoring & Alerting

### Key Metrics to Monitor

1. **Authentication Metrics**:
   - Failed login attempts per user/IP
   - Successful logins per hour
   - Account lockouts
   - Password reset requests

2. **Token Metrics**:
   - Token issuance rate
   - Token revocation rate
   - Expired token usage attempts
   - Token scope violations

3. **Authorization Metrics**:
   - Permission denied events
   - Role changes
   - Permission assignments
   - Unauthorized access attempts

4. **System Metrics**:
   - Error rates
   - Response times
   - Database query patterns
   - API usage patterns

### Alert Thresholds

- **CRITICAL**: > 10 failed logins từ same IP trong 5 minutes
- **HIGH**: > 5 account lockouts trong 1 hour
- **MEDIUM**: > 100 failed logins trong 1 hour
- **LOW**: Unusual access patterns

### Logging Requirements

```php
// ✅ GOOD: Security event logging
Log::channel('security')->info('User authenticated', [
    'user_id' => $userIdentity->id()->toString(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now()->toIso8601String(),
    // NO passwords, tokens, or sensitive data
]);

Log::channel('security')->warning('Failed authentication attempt', [
    'username' => $dto->username, // OK to log username
    'ip_address' => request()->ip(),
    'timestamp' => now()->toIso8601String(),
    // NO passwords
]);
```

---

## Security Training & Awareness

### Developer Security Training Topics

1. **OWASP Top 10**: Understanding common vulnerabilities
2. **Secure Coding Practices**: Best practices for secure code
3. **Authentication & Authorization**: Proper implementation
4. **Input Validation**: Comprehensive validation strategies
5. **Error Handling**: Secure error handling practices
6. **Cryptography**: Proper use of encryption và hashing
7. **Session Management**: Secure session handling
8. **Token Security**: OAuth2 và JWT security

### Security Awareness Checklist

- [ ] ✅ All developers trained on security best practices
- [ ] ✅ Security review process understood
- [ ] ✅ Incident response plan known
- [ ] ✅ Security tools và processes familiar
- [ ] ✅ Regular security updates và training

---

## Final Security Checklist Before Production

### Pre-Production Security Audit

- [ ] ✅ **Code Review**: Security-focused code review completed
- [ ] ✅ **Dependency Scan**: No known vulnerabilities (use `composer audit`)
- [ ] ✅ **Penetration Test**: Professional pen test completed và issues resolved
- [ ] ✅ **Security Headers**: All security headers configured và tested
- [ ] ✅ **HTTPS**: HTTPS only, HSTS enabled, valid SSL certificate
- [ ] ✅ **Rate Limiting**: Rate limiting configured và tested
- [ ] ✅ **Monitoring**: Security monitoring configured và tested
- [ ] ✅ **Alerting**: Security alerts configured và tested
- [ ] ✅ **Incident Response**: Incident response plan documented và tested
- [ ] ✅ **Backup Security**: Backups encrypted và secure
- [ ] ✅ **Access Control**: Proper access controls in place
- [ ] ✅ **Documentation**: Security documentation complete
- [ ] ✅ **Training**: Team trained on security procedures
- [ ] ✅ **Compliance**: Compliance requirements met (if applicable)

### Production Security Hardening

- [ ] ✅ **Environment Variables**: All secrets in environment variables
- [ ] ✅ **Debug Mode**: Debug mode disabled (`APP_DEBUG=false`)
- [ ] ✅ **Error Display**: Error display disabled
- [ ] ✅ **Logging**: Proper logging configured (no sensitive data)
- [ ] ✅ **Session Security**: Secure session configuration
- [ ] ✅ **Cookie Security**: Secure cookie configuration
- [ ] ✅ **CORS**: Restrictive CORS policy
- [ ] ✅ **Firewall**: Firewall rules configured
- [ ] ✅ **Database**: Database access restricted
- [ ] ✅ **File Permissions**: Proper file permissions set

---

**⚠️ REMEMBER**: Security is not a one-time task. It requires continuous monitoring, updates, và improvements. Always stay vigilant và keep security as the top priority.

---

## Security Code Examples Reference

### ✅ Secure Password Handling

```php
// ✅ GOOD: Hash password immediately
$passwordHash = PasswordHash::fromPlainText(
    $plainPassword,
    fn($p) => Hash::make($p) // bcrypt with cost 10
);

// ✅ GOOD: Verify password với timing-safe comparison
$isValid = $passwordHash->verify(
    $plainPassword,
    fn($p, $h) => Hash::check($p, $h) // Timing-safe
);

// ✅ GOOD: Never expose password hash
public function __toString(): string
{
    return '[REDACTED]';
}
```

### ✅ Secure Authentication

```php
// ✅ GOOD: Rate limiting
$key = 'authenticate:' . $username;
if (RateLimiter::tooManyAttempts($key, 5)) {
    Log::channel('security')->warning('Rate limit exceeded', [
        'username' => $username,
        'ip_address' => request()->ip(),
    ]);
    throw InvalidCredentialsException::invalid();
}

// ✅ GOOD: Generic error message
try {
    $userIdentity = $this->findUserIdentity($username);
    if ($userIdentity === null) {
        RateLimiter::hit($key);
        throw InvalidCredentialsException::invalid(); // Generic
    }
} catch (InvalidCredentialsException $e) {
    RateLimiter::hit($key);
    throw InvalidCredentialsException::invalid(); // Generic, no info leak
}

// ✅ GOOD: Clear rate limiter on success
RateLimiter::clear($key);
```

### ✅ Secure Token Management

```php
// ✅ GOOD: Token với expiration và scope
$token = $this->tokenGenerator->generateAccessToken(
    $userIdentity,
    $client,
    scopes: ['read', 'write'],
    expiresIn: 3600 // 1 hour
);

// ✅ GOOD: Validate token properly
$userInfo = $this->tokenGenerator->validateToken($token);
if ($userInfo === null) {
    throw InvalidTokenException::invalid();
}

if ($userInfo['expires_at'] < now()) {
    throw InvalidTokenException::expired();
}

if (!in_array('write', $userInfo['scopes'])) {
    throw InsufficientPermissionException::forScope('write');
}
```

### ✅ Secure Input Validation

```php
// ✅ GOOD: Whitelist validation
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
    throw new InvalidArgumentException('Invalid username format');
}

// ✅ GOOD: Reject SQL injection patterns
if (preg_match('/(\b(OR|AND|UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b)/i', $input)) {
    throw new InvalidArgumentException('Invalid input pattern');
}

// ✅ GOOD: Reject XSS patterns
if (preg_match('/<script|javascript:|onerror=|onload=/i', $input)) {
    throw new InvalidArgumentException('Invalid input pattern');
}
```

### ✅ Secure Error Handling

```php
// ✅ GOOD: Generic error message
try {
    $userIdentity = $this->authenticateUserUseCase->execute($dto);
} catch (InvalidCredentialsException $e) {
    // Generic message, no info leak
    return response()->json([
        'error' => 'Invalid credentials'
    ], 401);
} catch (\Exception $e) {
    // Log full error internally
    Log::error('Authentication error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    // Generic message to user
    return response()->json([
        'error' => 'An error occurred. Please try again later.'
    ], 500);
}
```

### ✅ Secure Logging

```php
// ✅ GOOD: Log security events without sensitive data
Log::channel('security')->info('User authenticated', [
    'user_id' => $userIdentity->id()->toString(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now()->toIso8601String(),
    // NO passwords, tokens, or sensitive data
]);

// ✅ GOOD: Log failed attempts for monitoring
Log::channel('security')->warning('Failed authentication attempt', [
    'username' => $dto->username, // OK to log username
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now()->toIso8601String(),
    // NO passwords
]);
```

### ✅ Secure Session Management

```php
// ✅ GOOD: Secure session configuration
config(['session' => [
    'driver' => 'database',
    'lifetime' => 120, // 2 hours
    'secure' => true, // HTTPS only
    'http_only' => true, // No JavaScript access
    'same_site' => 'strict', // CSRF protection
]]);
```

### ✅ Secure Authorization

```php
// ✅ GOOD: Check permissions at multiple layers
// Domain Layer
if (!$userIdentity->hasPermission('user.create')) {
    throw InsufficientPermissionException::forPermission('user.create');
}

// Application Layer
if (!$this->checkPermissionUseCase->execute($userIdentity, 'user.create')) {
    throw InsufficientPermissionException::forPermission('user.create');
}

// Infrastructure Layer (Middleware)
if (!auth()->user()->can('create', User::class)) {
    abort(403, 'Insufficient permissions');
}
```

### ❌ Insecure Code Examples (Anti-Patterns)

```php
// ❌ BAD: Plain password storage
$this->password = $plainPassword;

// ❌ BAD: Weak hashing
$hash = md5($password);

// ❌ BAD: Information leakage
throw new Exception("User {$email} not found");

// ❌ BAD: SQL injection vulnerability
DB::select("SELECT * FROM users WHERE email = '{$email}'");

// ❌ BAD: XSS vulnerability
echo $userInput;

// ❌ BAD: Timing attack vulnerability
if ($providedSecret === $storedSecret) {
    // Vulnerable
}

// ❌ BAD: Password in logs
Log::info('Login', ['password' => $password]);

// ❌ BAD: No rate limiting
public function login($credentials) {
    // No protection against brute force
}
```

---

## Security Review Process

### Step-by-Step Security Review

1. **Pre-Implementation Review**:
   - [ ] Threat modeling completed
   - [ ] Security requirements identified
   - [ ] Security patterns selected

2. **During Implementation**:
   - [ ] Follow security patterns
   - [ ] Write security tests
   - [ ] Self-review với security lens

3. **Pre-Commit Review**:
   - [ ] Run security checklist
   - [ ] Verify no sensitive data in code
   - [ ] Verify security tests pass

4. **Code Review**:
   - [ ] Security-focused review
   - [ ] Check for security anti-patterns
   - [ ] Verify security best practices

5. **Pre-Merge Review**:
   - [ ] Security expert review (if critical)
   - [ ] Dependency vulnerability scan
   - [ ] Security regression tests

6. **Pre-Production Review**:
   - [ ] Penetration testing
   - [ ] Security audit
   - [ ] Security configuration review

---

## Security Resources & References

### OWASP Resources
- **OWASP Top 10**: https://owasp.org/www-project-top-ten/
- **OWASP Authentication Cheat Sheet**: https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html
- **OWASP Password Storage Cheat Sheet**: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html

### Laravel Security
- **Laravel Security Documentation**: https://laravel.com/docs/security
- **Laravel Passport**: https://laravel.com/docs/passport
- **Laravel Sanctum**: https://laravel.com/docs/sanctum

### PHP Security
- **PHP Security Cheat Sheet**: https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html
- **PHP The Right Way - Security**: https://phptherightway.com/#security

### Security Tools
- **Composer Audit**: `composer audit` (built-in)
- **PHPStan**: Static analysis
- **SonarQube**: Code quality và security
- **OWASP ZAP**: Penetration testing tool

---

**🎯 FINAL REMINDER**: Security is the #1 priority for Phase 3. Every line of code must be reviewed với security lens. When in doubt, choose the more secure option. Better to be overly cautious than to have a security breach.

---

## Deployment Strategy

**⚠️ CRITICAL**: Phase 3 là **SECURITY-CRITICAL** phase. Deployment phải cực kỳ cẩn thận và có rollback plan rõ ràng.

### Pre-Deployment Checklist

**Trước khi deploy Phase 3, đảm bảo**:

- [ ] ✅ **All tests pass** (100% pass rate, >= 95% coverage)
- [ ] ✅ **Security review completed** và approved bởi security expert
- [ ] ✅ **Penetration testing completed** (nếu possible)
- [ ] ✅ **Code review completed** với security focus
- [ ] ✅ **Database backup created**
- [ ] ✅ **Staging environment tested** thoroughly với security tests
- [ ] ✅ **Feature flags configured** cho authentication/authorization
- [ ] ✅ **Rollback plan ready** và tested
- [ ] ✅ **Monitoring setup** với security alerts
- [ ] ✅ **Incident response plan** ready
- [ ] ✅ **Team notified** về deployment
- [ ] ✅ **Security team notified** và available

---

### Deployment Steps

#### Step 1: Pre-Deployment (1 giờ)

1. [ ] **Create deployment branch**:
   ```bash
   git checkout -b release/phase3-v1.0
   git push origin release/phase3-v1.0
   ```

2. [ ] **Final security verification**:
   ```bash
   # Run all tests
   php artisan test
   
   # Run security tests
   php artisan test --filter=SecurityTest
   
   # Check for vulnerabilities
   composer audit
   
   # Check code quality
   ./vendor/bin/pint --test
   ./vendor/bin/phpstan analyse
   ```

3. [ ] **Database backup**:
   ```bash
   php artisan db:backup
   # Backup cả authentication data
   ```

4. [ ] **Security checklist**:
   - [ ] No passwords/secrets in code
   - [ ] No sensitive data in logs
   - [ ] Rate limiting configured
   - [ ] Security headers configured
   - [ ] HTTPS enabled
   - [ ] Token validation proper

---

#### Step 2: Staging Deployment (2 giờ)

1. [ ] **Deploy to staging**:
   ```bash
   git checkout release/phase3-v1.0
   git pull origin release/phase3-v1.0
   
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan passport:install --force  # If needed
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   ```

2. [ ] **Staging security testing**:
   - [ ] Test authentication flow
   - [ ] Test authorization checks
   - [ ] Test token validation
   - [ ] Test rate limiting
   - [ ] Test password security
   - [ ] Test Microsoft authentication
   - [ ] Security penetration testing (nếu possible)

3. [ ] **Fix any security issues** found

---

#### Step 3: Production Deployment (2 giờ)

**⚠️ CRITICAL**: Phase 3 deployment phải có feature flags và gradual rollout.

1. [ ] **Enable feature flags** (OFF initially):
   ```php
   // config/features.php
   'identity_access_ddd' => env('FEATURE_DDD_IDENTITY_ACCESS', false),
   'new_authentication' => env('FEATURE_NEW_AUTH', false),
   'new_authorization' => env('FEATURE_NEW_AUTHORIZATION', false),
   ```

2. [ ] **Deploy code** (features OFF):
   ```bash
   git checkout release/phase3-v1.0
   git pull origin release/phase3-v1.0
   
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan passport:install --force  # If needed
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   ```

3. [ ] **Verify production** (features OFF):
   - [ ] Old authentication works
   - [ ] Old authorization works
   - [ ] No errors
   - [ ] Performance OK

4. [ ] **Gradual rollout**:
   - **Day 1**: Enable cho internal users only
   - **Day 2-3**: Monitor, fix issues
   - **Day 4-5**: Enable cho 25% users
   - **Day 6-7**: Monitor, enable cho 50% users
   - **Day 8-9**: Monitor, enable cho 100% users

5. [ ] **Monitor security**:
   - [ ] Failed login attempts
   - [ ] Token validation errors
   - [ ] Authorization failures
   - [ ] Suspicious activity
   - [ ] Performance metrics

---

#### Step 4: Post-Deployment Verification (1 giờ)

1. [ ] **Security smoke tests**:
   - [ ] Test login với valid credentials
   - [ ] Test login với invalid credentials (rate limiting)
   - [ ] Test token validation
   - [ ] Test permission checks
   - [ ] Test Microsoft authentication

2. [ ] **Check security logs**:
   ```bash
   tail -f storage/logs/security.log
   # Check for security events
   ```

3. [ ] **Check monitoring**:
   - [ ] Error rate (should be 0)
   - [ ] Authentication success rate
   - [ ] Token validation success rate
   - [ ] Authorization success rate
   - [ ] Rate limiting triggers
   - [ ] Security alerts

4. [ ] **Check database**:
   - [ ] UserIdentity data integrity
   - [ ] Roles và permissions data
   - [ ] Token data (if stored)

5. [ ] **User acceptance**:
   - [ ] Test với internal users
   - [ ] Monitor user feedback
   - [ ] Check for authentication issues

---

### Rollback Procedures

**Nếu có security issues sau deployment**:

#### Immediate Rollback (Critical Security Issue)

1. [ ] **Disable feature flags IMMEDIATELY**:
   ```bash
   # Update .env
   FEATURE_DDD_IDENTITY_ACCESS=false
   FEATURE_NEW_AUTH=false
   FEATURE_NEW_AUTHORIZATION=false
   php artisan config:cache
   ```

2. [ ] **Put application in maintenance mode** (nếu critical):
   ```bash
   php artisan down --message="Security update in progress"
   ```

3. [ ] **Investigate** security issue:
   - [ ] Check security logs
   - [ ] Check error logs
   - [ ] Check monitoring alerts
   - [ ] Identify root cause

4. [ ] **Fix** security issue

5. [ ] **Test fix** thoroughly

6. [ ] **Redeploy** với fix

#### Full Rollback (Code)

1. [ ] **Stop application**:
   ```bash
   php artisan down
   ```

2. [ ] **Revert code**:
   ```bash
   git checkout <previous-stable-version>
   git pull origin <previous-stable-version>
   ```

3. [ ] **Revert migrations** (cẩn thận với authentication data):
   ```bash
   # Review migrations trước khi rollback
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

6. [ ] **Verify** authentication/authorization works

7. [ ] **Document** security issue và resolution

---

### Feature Flags Strategy

**Implementation**:

1. [ ] **Configure feature flags**:
   ```php
   // config/features.php
   return [
       'identity_access_ddd' => [
           'default' => env('FEATURE_DDD_IDENTITY_ACCESS', false),
           'description' => 'Enable DDD IdentityAccess Context',
       ],
       'new_authentication' => [
           'default' => env('FEATURE_NEW_AUTH', false),
           'description' => 'Enable new authentication flow',
       ],
       'new_authorization' => [
           'default' => env('FEATURE_NEW_AUTHORIZATION', false),
           'description' => 'Enable new authorization system',
       ],
   ];
   ```

2. [ ] **Use trong code**:
   ```php
   use Laravel\Feature\Feature;

   if (Feature::active('new_authentication')) {
       // Use new authentication
       $userIdentity = $this->authenticateUserUseCase->execute($dto);
   } else {
       // Use old authentication
       if (Auth::attempt($credentials)) {
           // ...
       }
   }
   ```

3. [ ] **Gradual rollout**:
   - **Week 1**: Internal users only
   - **Week 2**: 10% of users (monitor closely)
   - **Week 3**: 25% of users
   - **Week 4**: 50% of users
   - **Week 5**: 100% of users

---

### Security Monitoring Checklist

**After deployment, monitor**:

- [ ] ✅ **Failed login attempts** (should be normal, not spike)
- [ ] ✅ **Rate limiting triggers** (should work correctly)
- [ ] ✅ **Token validation errors** (should be 0)
- [ ] ✅ **Authorization failures** (should be expected, not errors)
- [ ] ✅ **Security events** (login, logout, permission changes)
- [ ] ✅ **Suspicious activity** (unusual patterns)
- [ ] ✅ **Performance** (authentication should be fast)
- [ ] ✅ **Error rate** (should be 0 for security code)

**Security Monitoring Tools**:
- Security event logs
- Failed login tracking
- Token validation monitoring
- Authorization decision logging
- Security alerts (Sentry, etc.)

---

### Deployment Timeline

**Recommended Timeline** (Conservative approach):

- **Week 1**: Deploy to staging, security testing
- **Week 2**: Deploy to production với feature flags OFF
- **Week 3**: Enable cho internal users, monitor
- **Week 4**: Enable cho 10% users, monitor closely
- **Week 5**: Enable cho 25% users, monitor
- **Week 6**: Enable cho 50% users, monitor
- **Week 7**: Enable cho 100% users, monitor
- **Week 8+**: Full rollout nếu no security issues

**⚠️ Note**: Phase 3 deployment nên conservative hơn các phases khác vì security-critical.
