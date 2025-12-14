# Kiến trúc tổng quát theo Domain-Driven Design (DDD)

Tài liệu này mô tả kiến trúc tổng quát được đề xuất để tái cấu trúc dự án theo các nguyên tắc của Domain-Driven Design (DDD). Mục tiêu là tạo ra một hệ thống linh hoạt, dễ bảo trì, dễ mở rộng và thể hiện rõ ràng logic nghiệp vụ.

## 1. Nguyên tắc cốt lõi

*   **Kiến trúc phân lớp (Layered Architecture):** Tách biệt rõ ràng các mối quan tâm của hệ thống thành các lớp độc lập.
*   **Bounded Context:** Phân chia hệ thống thành các module nghiệp vụ riêng biệt, mỗi module có ngôn ngữ và mô hình dữ liệu riêng.
*   **Dependency Inversion:** Các lớp cấp cao (nghiệp vụ) không phụ thuộc vào các lớp cấp thấp (cơ sở hạ tầng), mà phụ thuộc vào các Abstraction (interface).

## 2. Sơ đồ kiến trúc các lớp

Kiến trúc bao gồm 3 lớp chính, tuân thủ nguyên tắc phụ thuộc một chiều: lớp bên ngoài phụ thuộc vào lớp bên trong nó.

```
+--------------------------------------------------------------------------+
|                                                                          |
|   Infrastructure Layer (Lớp Cơ sở hạ tầng)                               |
|   (Controllers, Repositories, Providers, External Services...)           |
|                                                                          |
|   +----------------------------------------------------------------------+
|   |                                                                      |
|   |   Application Layer (Lớp Ứng dụng)                                   |
|   |   (Use Cases / Application Services, DTOs)                           |
|   |                                                                      |
|   |   +----------------------------------------------------------------+ |
|   |   |                                                                | |
|   |   |   Domain Layer (Lớp Miền)                                      | |
|   |   |   (Aggregates, Entities, Value Objects, Domain Events)         | |
|   |   |                                                                | |
|   |   +----------------------------------------------------------------+ |
|   |                                                                      |
|   +----------------------------------------------------------------------+
|                                                                          |
+--------------------------------------------------------------------------+
```

### 2.1. Domain Layer (Lớp Miền)

*   **Trách nhiệm:** Đây là "trái tim" của phần mềm. Chứa toàn bộ logic nghiệp vụ, quy tắc và trạng thái.

*   **Thành phần:**

    *   **Aggregates/Entities:**
        - Các đối tượng có vòng đời và định danh, chứa các hành vi nghiệp vụ (ví dụ: `User`, `Client`, `Role`)
        - Aggregate Root là điểm vào duy nhất để thao tác với aggregate
        - Chịu trách nhiệm đảm bảo tính nhất quán của aggregate
        - Phát sinh Domain Events khi có thay đổi quan trọng
        - Không có setters công khai, chỉ có các phương thức nghiệp vụ có ý nghĩa

    *   **Value Objects:**
        - Các đối tượng bất biến mô tả một thuộc tính, không có định danh (ví dụ: `Email`, `StudentCode`, `FullName`)
        - Tự validate dữ liệu khi khởi tạo
        - Implement `equals()` để so sánh
        - Có thể implement `Stringable` để dễ dàng convert sang string

    *   **Domain Events:**
        - Các sự kiện nghiệp vụ đã xảy ra (ví dụ: `UserWasCreated`, `StudentWasImported`)
        - Là các DTO bất biến chứa thông tin về sự kiện
        - Được tạo bởi Aggregate khi có thay đổi quan trọng
        - Có phương thức `toPayload()` để serialize

    *   **Repository Interfaces:**
        - Các hợp đồng (interface) định nghĩa các phương thức để truy xuất và lưu trữ Aggregates
        - Giúp lớp Domain không phụ thuộc vào cơ sở dữ liệu
        - Chỉ làm việc với Domain Objects, không có Eloquent models
        - Có thể có các phương thức như `findById()`, `save()`, `delete()`, `nextIdentity()`

    *   **Domain Services (nếu cần):**
        - Các dịch vụ chứa logic nghiệp vụ không thuộc về một Aggregate cụ thể
        - Ví dụ: `PasswordHasher`, `TokenGenerator`
        - Chỉ được sử dụng khi logic không thể đặt trong Aggregate

    *   **Domain Exceptions:**
        - Các exception đặc thù cho domain
        - Ví dụ: `UserNotFoundException`, `InvalidEmailException`, `InsufficientPermissionException`

*   **Đặc điểm:**
    - Hoàn toàn "trong sáng", không chứa bất kỳ mã nguồn nào liên quan đến cơ sở dữ liệu, framework, hay giao diện người dùng
    - Không có dependency vào Laravel (trừ một số trường hợp đặc biệt như Hash facade)
    - Có thể test độc lập mà không cần database hay framework
    - Chứa toàn bộ business rules và validation logic

### 2.2. Application Layer (Lớp Ứng dụng)

*   **Trách nhiệm:** Điều phối các hành vi của lớp Domain để thực hiện một kịch bản sử dụng (use case) cụ thể.

*   **Thành phần:**

    *   **Application Services / Use Cases:**
        - Các lớp điều phối, nhận yêu cầu từ bên ngoài (ví dụ: từ Controller)
        - Mỗi Use Case đại diện cho một kịch bản sử dụng cụ thể
        - Luồng xử lý điển hình:
          1. Nhận DTO từ Controller
          2. Validate input (có thể delegate cho Request class)
          3. Sử dụng Repository để lấy Aggregates
          4. Gọi các phương thức nghiệp vụ trên Aggregates
          5. Lưu lại trạng thái thông qua Repository
          6. Xử lý Domain Events (lưu vào Outbox nếu cần)
          7. Trả về kết quả (DTO hoặc Domain Object)
        - Có thể gọi nhiều Repository hoặc Domain Service
        - Có thể gọi Use Case khác trong cùng BC hoặc BC khác (đồng bộ)
        - Xử lý transaction (wrap trong DB::transaction)

    *   **Data Transfer Objects (DTOs):**
        - Các đối tượng đơn giản dùng để truyền dữ liệu vào và ra khỏi lớp Application
        - Không chứa logic nghiệp vụ, chỉ là data containers
        - Có thể có validation rules (nếu dùng với Form Request)
        - Input DTOs: Nhận dữ liệu từ Controller
        - Output DTOs: Trả về dữ liệu cho Controller
        - Có thể có factory methods để tạo từ Request hoặc Array

    *   **Application Services (nếu cần):**
        - Các dịch vụ ứng dụng không thuộc về một Use Case cụ thể
        - Ví dụ: `EmailService`, `NotificationService`
        - Có thể được inject vào Use Cases

*   **Đặc điểm:**
    - Không chứa logic nghiệp vụ. Nó chỉ là người điều phối "ai làm việc gì"
    - Có thể phụ thuộc vào Infrastructure layer thông qua interfaces (Dependency Inversion)
    - Mỗi Use Case nên có một trách nhiệm duy nhất (Single Responsibility)
    - Có thể throw Application Exceptions để Controller xử lý

### 2.3. Infrastructure Layer (Lớp Cơ sở hạ tầng)

*   **Trách nhiệm:** Cung cấp các chi tiết kỹ thuật, là cầu nối giữa ứng dụng và thế giới bên ngoài.

*   **Thành phần:**

    *   **Entry Points:**
        - **Controllers:** Nhận HTTP requests, validate input, gọi Use Cases, trả về HTTP responses
          - Nên mỏng, chỉ làm nhiệm vụ điều phối
          - Sử dụng Form Request để validate
          - Xử lý exceptions và trả về response phù hợp
          - Có thể sử dụng API Resources để format response
        - **Livewire Components:** Quản lý UI state, nhận user interactions, gọi Use Cases
          - Tương tự Controller nhưng cho real-time UI
          - Không chứa logic nghiệp vụ
          - Có thể dispatch browser events
        - **Console Commands:** Xử lý các tác vụ CLI
          - Ví dụ: Import data, Process outbox events, Generate reports

    *   **Repository Implementations:**
        - Các lớp cụ thể triển khai Repository Interfaces bằng công nghệ lưu trữ
        - Ví dụ: `EloquentUserRepository` sử dụng Eloquent ORM
        - Chịu trách nhiệm:
          - Chuyển đổi giữa Domain Objects và Persistence Models (Eloquent Models)
          - Thực thi các query phức tạp
          - Xử lý Domain Events (lưu vào Outbox hoặc dispatch)
        - Có thể có các phương thức helper để map giữa Domain và Eloquent

    *   **Event Listeners:**
        - Các lớp lắng nghe sự kiện từ các Bounded Context khác
        - Xử lý side effects không đồng bộ
        - Ví dụ: Gửi email khi user được tạo, cập nhật cache khi có thay đổi
        - Có thể queue để xử lý bất đồng bộ

    *   **Service Providers:**
        - Đăng ký các implementation với interface trong IoC container của Laravel
        - Bind Repository Interfaces với Implementations
        - Đăng ký Event Listeners
        - Cấu hình các services

    *   **External Service Clients:**
        - Code để gọi các API của bên thứ ba
        - Ví dụ: Microsoft Graph API client, Email service client
        - Nên có interface để dễ dàng mock khi testing

    *   **Middleware:**
        - Xử lý các cross-cutting concerns
        - Ví dụ: Authentication, Authorization, Rate limiting, CORS

    *   **Form Requests:**
        - Validate HTTP requests
        - Có thể authorize requests
        - Trả về validation errors

    *   **API Resources:**
        - Format dữ liệu trả về cho API
        - Transform Domain Objects thành JSON response

    *   **Jobs/Queues:**
        - Xử lý các tác vụ bất đồng bộ
        - Ví dụ: Send email, Process import, Generate report

*   **Đặc điểm:**
    - Là lớp "dễ thay đổi" nhất, phụ thuộc vào framework và các thư viện bên ngoài
    - Có thể thay đổi implementation mà không ảnh hưởng đến Domain và Application layers
    - Chứa tất cả các chi tiết kỹ thuật (database, HTTP, caching, etc.)

### 2.4. Cấu trúc thư mục tham khảo

Để hiện thực hóa kiến trúc các lớp trên, chúng ta áp dụng cấu trúc thư mục sau bên trong `app/`. Mỗi Bounded Context sẽ có một cấu trúc nội bộ nhất quán.

```
app/
├── 📂 IdentityAccess/  (BC 1: Quản lý định danh và truy cập)
│   ├── 📂 Application/
│   │   ├── 📂 DTOs/
│   │   └── 📂 UseCases/
│   ├── 📂 Domain/
│   │   ├── 📂 Aggregates/
│   │   ├── 📂 Entities/
│   │   ├── 📂 Events/
│   │   ├── 📂 Repositories/      (Interfaces)
│   │   └── 📂 ValueObjects/
│   └── 📂 Infrastructure/
│       ├── 📂 Console/            (Artisan Commands)
│       ├── 📂 Http/
│       │   ├── 📂 Controllers/
│       │   └── 📂 Livewire/     (Livewire Components)
│       ├── 📂 Listeners/          (Event Listeners)
│       ├── 📂 Persistence/        (Eloquent Repository Implementations)
│       └── 📂 Providers/          (Context-specific Service Providers)
│
├── 📂 OrganizationalStructure/ (BC 2: Quản lý cơ cấu tổ chức)
│   └── ... (cấu trúc tương tự như IdentityAccess)
│
└── 📂 SharedKernel/ (Lõi dùng chung cho toàn bộ hệ thống)
    └── 📂 Domain/
        ├── 📂 ValueObjects/
        └── 📂 Exceptions/
```

Cấu trúc này giúp nhóm mã nguồn theo nghiệp vụ (Bounded Context) thay vì theo chức năng kỹ thuật, làm cho việc điều hướng và phát triển các tính năng trở nên dễ dàng và logic hơn.

## 3. Bounded Contexts

Hệ thống được chia thành các Bounded Context (BC) để giảm sự phức tạp. Mỗi BC là một "mini-application" có Domain, Application, và Infrastructure layer riêng. Việc phân chia này giúp:

- **Giảm coupling:** Mỗi BC độc lập và có thể phát triển riêng biệt
- **Tăng cohesion:** Các thành phần liên quan được nhóm lại với nhau
- **Dễ bảo trì:** Thay đổi trong một BC không ảnh hưởng đến BC khác
- **Mở rộng dễ dàng:** Có thể thêm BC mới mà không làm ảnh hưởng đến các BC hiện có

### 3.1. IdentityAccess Context

**Mục đích:** Chịu trách nhiệm cho mọi thứ liên quan đến xác thực, phân quyền, quản lý vai trò và ứng dụng (clients).

**Các Aggregate chính:**
- **User (Identity):** Quản lý thông tin đăng nhập, mật khẩu, trạng thái tài khoản
- **Role:** Định nghĩa các vai trò trong hệ thống
- **Permission:** Định nghĩa các quyền hạn chi tiết
- **Client:** Quản lý các ứng dụng được phép sử dụng SSO
- **AccessToken:** Quản lý các token được cấp cho người dùng và ứng dụng

**Các Use Case chính:**
- `AuthenticateUserUseCase`: Xác thực người dùng bằng username/password
- `AuthenticateWithMicrosoftUseCase`: Xác thực người dùng qua Microsoft Azure
- `IssueAccessTokenUseCase`: Cấp access token cho người dùng/ứng dụng
- `ValidateTokenUseCase`: Xác thực token và trả về thông tin người dùng
- `CreateRoleUseCase`: Tạo vai trò mới
- `AssignPermissionToRoleUseCase`: Gán quyền cho vai trò
- `RegisterClientUseCase`: Đăng ký ứng dụng mới
- `RevokeTokenUseCase`: Thu hồi token

**Domain Events:**
- `UserWasAuthenticated`: Khi người dùng đăng nhập thành công
- `TokenWasIssued`: Khi token mới được cấp
- `TokenWasRevoked`: Khi token bị thu hồi
- `RoleWasCreated`: Khi vai trò mới được tạo
- `PermissionWasAssignedToRole`: Khi quyền được gán cho vai trò
- `ClientWasRegistered`: Khi ứng dụng mới được đăng ký

**Giao tiếp với các BC khác:**
- Lắng nghe `UserWasCreated` từ `OrganizationalStructure` để tự động tạo thông tin đăng nhập mặc định
- Lắng nghe `UserProfileWasUpdated` để cập nhật thông tin trong token (nếu cần)

### 3.2. OrganizationalStructure Context

**Mục đích:** Chịu trách nhiệm quản lý người dùng, hồ sơ, và cơ cấu tổ chức (khoa, phòng ban).

**Các Aggregate chính:**
- **User (Profile):** Quản lý thông tin cá nhân, hồ sơ người dùng (khác với User trong IdentityAccess - đây là về profile)
- **Faculty:** Quản lý thông tin các khoa
- **Department:** Quản lý thông tin các phòng ban
- **UserFacultyAssignment:** Liên kết giữa người dùng và khoa
- **UserDepartmentAssignment:** Liên kết giữa người dùng và phòng ban

**Các Use Case chính:**
- `CreateUserUseCase`: Tạo người dùng mới
- `UpdateUserProfileUseCase`: Cập nhật thông tin cá nhân
- `AssignUserToFacultyUseCase`: Gán người dùng vào khoa
- `AssignUserToDepartmentUseCase`: Gán người dùng vào phòng ban
- `CreateFacultyUseCase`: Tạo khoa mới
- `UpdateFacultyUseCase`: Cập nhật thông tin khoa
- `CreateDepartmentUseCase`: Tạo phòng ban mới
- `ImportUsersFromExcelUseCase`: Import người dùng từ file Excel
- `FindUsersByFacultyUseCase`: Tìm người dùng theo khoa
- `FindUsersByDepartmentUseCase`: Tìm người dùng theo phòng ban

**Domain Events:**
- `UserWasCreated`: Khi người dùng mới được tạo
- `UserProfileWasUpdated`: Khi thông tin cá nhân được cập nhật
- `UserWasAssignedToFaculty`: Khi người dùng được gán vào khoa
- `UserWasAssignedToDepartment`: Khi người dùng được gán vào phòng ban
- `FacultyWasCreated`: Khi khoa mới được tạo
- `DepartmentWasCreated`: Khi phòng ban mới được tạo
- `UsersWereImported`: Khi import người dùng từ Excel hoàn tất

**Giao tiếp với các BC khác:**
- Phát ra `UserWasCreated` để `IdentityAccess` có thể tạo thông tin đăng nhập
- Phát ra `UserProfileWasUpdated` để các hệ thống khác có thể cập nhật thông tin

### 3.3. Shared Kernel

**Mục đích:** Là một không gian chung chứa các mã nguồn được tái sử dụng bởi tất cả các Bounded Context.

**Các thành phần chính:**
- **Value Objects dùng chung:**
  - `Email`: Định dạng và validate email
  - `Uuid`: Định danh duy nhất
  - `Timestamp`: Thời gian với timezone
  - `Money`: Giá trị tiền tệ (nếu cần)

- **Exceptions dùng chung:**
  - `DomainException`: Exception cơ bản cho domain layer
  - `EntityNotFoundException`: Khi không tìm thấy entity
  - `InvalidArgumentException`: Khi tham số không hợp lệ

- **Interfaces dùng chung:**
  - `EventDispatcherInterface`: Interface để dispatch events
  - `ClockInterface`: Interface để lấy thời gian hiện tại (hữu ích cho testing)

- **Utilities:**
  - Các helper functions dùng chung
  - Các constants dùng chung

**Nguyên tắc:**
- Shared Kernel phải nhỏ và chỉ chứa những thứ thực sự cần thiết
- Tránh đưa logic nghiệp vụ vào Shared Kernel
- Các BC không được phụ thuộc quá nhiều vào Shared Kernel

## 4. Luồng xử lý một yêu cầu (Request Flow)

Một yêu cầu cập nhật thông tin người dùng sẽ đi qua các lớp như sau:

1.  **Request:** Người dùng gửi HTTP `PUT` request đến `/users/{id}`.
2.  **Infrastructure (Controller):** `UserController` nhận request, đóng gói dữ liệu vào một `UpdateUserDTO`.
3.  **Controller -> Application:** `UserController` gọi phương thức `handle(UpdateUserDTO $dto)` của `UpdateUserUseCase`.
4.  **Application (Use Case):**
    *   `UpdateUserUseCase` sử dụng `UserRepositoryInterface` để tìm `User` aggregate dựa trên ID.
    *   Gọi các phương thức nghiệp vụ trên `User` aggregate: `$user->changeName($dto->name)`.
    *   Sử dụng `UserRepositoryInterface` để lưu lại aggregate đã thay đổi.
5.  **Domain (Repository Interface):** Lớp Application chỉ biết đến interface.
6.  **Infrastructure (Repository Implementation):** IoC container của Laravel sẽ đưa vào `EloquentUserRepository`. Lớp này sẽ thực thi câu lệnh Eloquent để cập nhật dữ liệu trong cơ sở dữ liệu.
7.  **Response:** `UserController` nhận kết quả từ Use Case và trả về một HTTP Response (ví dụ: JSON).

## 5. Kiến trúc Event-Driven với Transactional Outbox Pattern

Để đảm bảo tính toàn vẹn dữ liệu và độ tin cậy tuyệt đối khi giao tiếp giữa các Bounded Context, chúng ta áp dụng **Transactional Outbox Pattern**. Pattern này giải quyết vấn đề "dual-write" (ghi vào DB và gửi event ra message bus là hai hành động riêng lẻ), đảm bảo rằng một event chỉ được gửi đi **khi và chỉ khi** transaction của nghiệp vụ chính đã thành công.

### 5.1. Tổng quan luồng xử lý

```
[Business DB Transaction]                                  [Message Bus / Queue]
+------------------------------------+
| Application Service (Use Case)     |
|  1. Bắt đầu DB Transaction         |
|  2. Lưu Aggregate (User) vào DB    |
|  3. Lưu Event vào bảng 'outbox'    | --(COMMIT)--> [Database]
+------------------------------------+                      |
                                                           | (Bảng users và outbox_events
                                                           |  được cập nhật nguyên tử)
                                                           |
       [Async Relay Process (Scheduled Command)]           |
       +-----------------------------------------+           |
[DB] <--(4. Đọc event chưa xử lý từ 'outbox')<--|           |
       |                                         |           |
       |  5. Dispatch Event tới Message Bus      | --(PUBLISH)--> [Redis / SQS]
       |                                         |
       |  6. Đánh dấu event đã xử lý trong 'outbox'|
       +-----------------------------------------+

                                                            [Context B]
                                                            +----------------+
[Queue Worker] --(7. Lấy message & thực thi)--> Executes --> | Event Listener |
                                                            +----------------+
```

### 5.2. Mẫu và ví dụ cho từng thành phần

---

#### A. The Outbox Migration (Tạo bảng Outbox)

*   **Vị trí:** `database/migrations/`
*   **Mô tả:** Một bảng trong cơ sở dữ liệu chính để lưu trữ các event một cách tạm thời và đáng tin cậy.
*   **Quy ước:** Bảng này là trung tâm của pattern.

**Mẫu (Tạo migration `create_outbox_events_table`):**
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
            $table->string('aggregate_type');
            $table->uuid('aggregate_id');
            $table->string('event_type');
            $table->json('payload');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
        });
    }
    // ... down() method
};
```

---

#### B. The Domain Event

*   **Vị trí:** `app/{BoundedContext}/Domain/Events/`
*   **Mô tả:** Không thay đổi so với kiến trúc trước, vẫn là một DTO bất biến. Có thể thêm một phương thức `payload()` để dễ dàng serialize.

**Mẫu (`app/OrganizationalStructure/Domain/Events/UserWasCreated.php`):**
```php
<?php
// ...
final class UserWasCreated
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $fullName,
    ) {}

    /**
     * Chuyển đổi event thành một mảng để lưu vào payload.
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'full_name' => $this->fullName,
        ];
    }
}
```

---

#### C. Storing the Event to Outbox (Lưu Event)

*   **Vị trí:** Bên trong Application Service (Use Case), được bao bọc bởi một DB transaction.
*   **Mô tả:** Thay vì gọi `event()`, chúng ta sẽ lưu record mới vào bảng `outbox_events`.
*   **Quy ước:** Việc lưu Aggregate và lưu Event vào Outbox phải nằm trong cùng một transaction.

**Mẫu (Trích đoạn từ `app/OrganizationalStructure/Application/UseCases/CreateUserUseCase.php`):**
```php
<?php
// ...
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateUserUseCase
{
    // ...
    public function handle(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            // 1. Logic tạo user aggregate
            $user = User::create(/* ... */);
            $this->userRepository->save($user); // Lưu vào bảng 'users'

            // 2. Chuẩn bị và lưu event vào bảng 'outbox_events'
            $event = new UserWasCreated(/* ... */);
            DB::table('outbox_events')->insert([
                'id' => Str::uuid()->toString(),
                'aggregate_type' => 'user',
                'aggregate_id' => $user->id(),
                'event_type' => UserWasCreated::class,
                'payload' => json_encode($event->toPayload()),
            ]);

            return $user;
        });
    }
}
```

---

#### D. The Relay Process (Tiến trình chuyển tiếp)

*   **Vị trí:** Một Artisan Command, ví dụ: `app/SharedKernel/Infrastructure/Console/ProcessOutboxCommand.php`.
*   **Mô tả:** Một tiến trình nền (background process), được chạy định kỳ (ví dụ: mỗi phút) bởi Laravel Scheduler.
*   **Quy ước:** Command này có trách nhiệm đọc các event từ outbox và dispatch chúng một cách an toàn ra message bus thực sự.

**Mẫu (`ProcessOutboxCommand.php`):**
```php
<?php
// ...
use Illuminate\Support\Facades\DB;

class ProcessOutboxCommand extends Command
{
    protected $signature = 'outbox:process';

    public function handle(): void
    {
        DB::table('outbox_events')
            ->whereNull('processed_at')
            ->orderBy('created_at')
            ->chunk(100, function ($events) {
                foreach ($events as $outboxEvent) {
                    // Tái tạo lại đối tượng Event từ payload
                    // (Cần một EventMapper hoặc logic tương ứng)
                    $domainEvent = $this->reconstituteEvent($outboxEvent);

                    // Dispatch ra bus thực sự
                    event($domainEvent);

                    // Đánh dấu đã xử lý
                    DB::table('outbox_events')
                        ->where('id', $outboxEvent->id)
                        ->update(['processed_at' => now()]);
                }
            });
    }
    // ... reconstituteEvent logic
}
```
**Đăng ký trong `app/Console/Kernel.php`:**
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('outbox:process')->everyMinute()->withoutOverlapping();
}
```

---

#### E. The Event Listener & Registration

*   **Không thay đổi:** Phần `Listener` và `EventServiceProvider` giữ nguyên như kiến trúc cũ.
*   **Lý do:** Listener không cần biết event đến từ đâu. Nó chỉ phản ứng khi Laravel Event Bus (được gọi bởi Relay Process) giao event cho nó. Sự phức tạp của Outbox đã được đóng gói hoàn toàn.

---

Kiến trúc này mang lại sự đảm bảo "at-least-once delivery" và sự toàn vẹn dữ liệu, là nền tảng vững chắc cho một hệ thống microservices hoặc các hệ thống có yêu cầu cao về độ tin cậy.
