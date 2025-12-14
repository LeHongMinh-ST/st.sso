# Ví dụ mã nguồn mẫu theo Kiến trúc DDD

Tài liệu này cung cấp các ví dụ mã nguồn và mẫu chuẩn cho các thành phần chính trong kiến trúc DDD của dự án. Mục tiêu là để các lập trình viên có một "khuôn mẫu vàng" để tuân theo, đảm bảo tính nhất quán và chất lượng mã nguồn.

---

## 1. Lớp Domain (The Domain Layer)

Đây là lớp cốt lõi chứa toàn bộ nghiệp vụ.

### 1.1. Value Object

*   **Mô tả:** Một đối tượng bất biến (immutable) mô tả một thuộc tính và không có định danh. Nó tự thực hiện việc xác thực (validation) chính nó.
*   **Vị trí:** `app/{Context}/Domain/ValueObjects/`

**Mẫu (`app/OrganizationalStructure/Domain/ValueObjects/StudentCode.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\ValueObjects;

use Stringable;
use InvalidArgumentException;

/**
 * Đại diện cho Mã số sinh viên.
 * Bất biến và luôn hợp lệ.
 */
final class StudentCode implements Stringable
{
    private const VALID_PATTERN = '/^B\d{8}$/'; // Ví dụ: B12345678

    private string $value;

    /**
     * @param string $value
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        if (!preg_match(self::VALID_PATTERN, $value)) {
            throw new InvalidArgumentException("Invalid student code format: {$value}");
        }
        $this->value = $value;
    }

    /**
     * Phương thức khởi tạo tĩnh (static factory method).
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Lấy giá trị chuỗi.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * So sánh sự bằng nhau với một Value Object khác.
     *
     * @param StudentCode $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### 1.2. Aggregate Root

*   **Mô tả:** Là một Entity đặc biệt, đóng vai trò là "cổng" cho mọi sự thay đổi đối với một nhóm các đối tượng liên quan (gọi là Aggregate). Mọi tương tác từ bên ngoài phải đi qua Aggregate Root. Nó chịu trách nhiệm duy trì sự nhất quán của toàn bộ Aggregate và phát sinh ra các Domain Event.
*   **Vị trí:** `app/{Context}/Domain/Aggregates/`

**Mẫu (`app/OrganizationalStructure/Domain/Aggregates/User.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Aggregates;

use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\Email;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use App\OrganizationalStructure\Domain\Events\UserProfileWasUpdated;
use Illuminate\Support\Facades\Hash;

/**
 * Aggregate Root cho người dùng.
 */
class User
{
    private array $domainEvents = [];

    // Các thuộc tính khác là các Value Object
    private UserId $id;
    private FullName $fullName;
    private Email $email;
    private string $passwordHash;

    private function __construct(UserId $id, FullName $fullName, Email $email)
    {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
    }

    /**
     * Factory method để tạo User mới.
     */
    public static function create(UserId $id, FullName $fullName, Email $email, string $plainTextPassword): self
    {
        $user = new self($id, $fullName, $email);
        $user->passwordHash = Hash::make($plainTextPassword);
        
        // Ghi lại sự kiện
        $user->recordEvent(new UserWasCreated($id->toString(), $email->toString(), $fullName->toString()));

        return $user;
    }

    /**
     * Hành vi nghiệp vụ: Cập nhật thông tin.
     */
    public function updateProfile(FullName $newFullName, Email $newEmail): void
    {
        $this->fullName = $newFullName;
        $this->email = $newEmail;

        $this->recordEvent(new UserProfileWasUpdated($this->id->toString()));
    }
    
    // Các getters để lấy dữ liệu, không có setters
    public function id(): UserId { return $this->id; }
    public function fullName(): FullName { return $this->fullName; }
    public function email(): Email { return $this->email; }

    /**
     * Ghi nhận một domain event.
     */
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
    
    /**
     * Lấy và xóa các domain events đã ghi nhận.
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

### 1.3. Repository Interface

*   **Mô tả:** Một interface định nghĩa các phương thức để lưu và truy xuất Aggregate Root, hoạt động như một "bộ sưu tập" các aggregate trong bộ nhớ. Nó thuộc về lớp Domain vì nó mô tả một yêu cầu của nghiệp vụ, nhưng việc triển khai (implementation) nó lại thuộc về Infrastructure.
*   **Vị trí:** `app/{Context}/Domain/Repositories/`

**Mẫu (`app/OrganizationalStructure/Domain/Repositories/UserRepositoryInterface.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Repositories;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\Email;

interface UserRepositoryInterface
{
    /**
     * Tạo một định danh mới cho User.
     */
    public function nextIdentity(): UserId;

    /**
     * Tìm User bằng ID.
     */
    public function findById(UserId $id): ?User;

    /**
     * Tìm User bằng Email.
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Lưu một User aggregate.
     */
    public function save(User $user): void;
}
```

---

## 2. Lớp Application (The Application Layer)

### 2.0. Data Transfer Object (DTO)

*   **Mô tả:** DTO là các đối tượng đơn giản dùng để truyền dữ liệu giữa các layer. Chúng không chứa logic nghiệp vụ, chỉ là data containers.
*   **Vị trí:** `app/{Context}/Application/DTOs/`

**Mẫu (`app/OrganizationalStructure/Application/DTOs/UpdateUserProfileDTO.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for updating user profile.
 */
final class UpdateUserProfileDTO
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     */
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['email'] ?? '',
        );
    }

    /**
     * Create DTO from Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('email'),
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
```

**Mẫu Output DTO (`app/OrganizationalStructure/Application/DTOs/UserDTO.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\DTOs;

/**
 * Data Transfer Object for user data output.
 */
final class UserDTO
{
    /**
     * @param string $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string|null $facultyName
     * @param string|null $departmentName
     */
    public function __construct(
        public readonly string $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $facultyName = null,
        public readonly ?string $departmentName = null,
    ) {
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function fullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
```

---

### 2.1. Use Case (Application Service)

*   **Mô tả:** Lớp điều phối thực hiện một kịch bản nghiệp vụ. Nó nhận DTO, dùng repository để lấy aggregate, gọi phương thức trên aggregate, và dùng repository để lưu lại. Nó không chứa logic nghiệp vụ.
*   **Vị trí:** `app/{Context}/Application/UseCases/`

**Mẫu (`app/OrganizationalStructure/Application/UseCases/UpdateUserProfileUseCase.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\Email;

final class UpdateUserProfileUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(string $userId, UpdateUserProfileDTO $dto): void
    {
        $user = $this->userRepository->findById(UserId::fromString($userId));
        
        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->updateProfile(
            new FullName($dto->firstName, $dto->lastName),
            new Email($dto->email)
        );

        $this->userRepository->save($user);
        
        // Logic dispatch event từ aggregate có thể nằm trong `save` method của repository
    }
}
```

**Mẫu Use Case với Transaction và Outbox (`app/OrganizationalStructure/Application/UseCases/CreateUserUseCase.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\Email;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Create a new user.
     *
     * @param CreateUserDTO $dto
     * @return User
     * @throws \App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException
     */
    public function handle(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            // 1. Check if user already exists
            $existingUser = $this->userRepository->findByEmail(
                Email::fromString($dto->email)
            );
            
            if ($existingUser) {
                throw new \App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException(
                    "User with email {$dto->email} already exists"
                );
            }

            // 2. Create user aggregate
            $userId = $this->userRepository->nextIdentity();
            $user = User::create(
                $userId,
                FullName::fromStrings($dto->firstName, $dto->lastName),
                Email::fromString($dto->email),
                $dto->password
            );

            // 3. Save user
            $this->userRepository->save($user);

            // 4. Get domain events and save to outbox
            $domainEvents = $user->pullDomainEvents();
            foreach ($domainEvents as $event) {
                DB::table('outbox_events')->insert([
                    'id' => Str::uuid()->toString(),
                    'aggregate_type' => 'user',
                    'aggregate_id' => $user->id()->toString(),
                    'event_type' => get_class($event),
                    'payload' => json_encode($this->eventToPayload($event)),
                    'created_at' => now(),
                ]);
            }

            return $user;
        });
    }

    /**
     * Convert domain event to payload array.
     *
     * @param object $event
     * @return array<string, mixed>
     */
    private function eventToPayload(object $event): array
    {
        if (method_exists($event, 'toPayload')) {
            return $event->toPayload();
        }

        // Fallback: convert public properties to array
        return get_object_vars($event);
    }
}
```

---

## 3. Lớp Infrastructure (The Infrastructure Layer)

### 3.1. Repository Implementation

*   **Mô tả:** Lớp triển khai cụ thể của Repository Interface, sử dụng một công nghệ lưu trữ như Eloquent. Đây là nơi chứa code "bẩn", phụ thuộc framework. Nó có nhiệm vụ chuyển đổi giữa Domain Model (Aggregate) và Persistence Model (Eloquent Model).
*   **Vị trí:** `app/{Context}/Infrastructure/Persistence/`

**Mẫu (`app/OrganizationalStructure/Infrastructure/Persistence/EloquentUserRepository.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Persistence;

use App\Models\User as EloquentUser; // Đây là Eloquent model
use App\OrganizationalStructure\Domain\Aggregates\User as DomainUser;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
// ... use các Value Object ...

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(UserId $id): ?DomainUser
    {
        $eloquentUser = EloquentUser::find($id->toString());
        if (!$eloquentUser) {
            return null;
        }
        return $this->toDomain($eloquentUser);
    }

    public function save(DomainUser $domainUser): void
    {
        $eloquentUser = EloquentUser::findOrNew($domainUser->id()->toString());
        // Chuyển đổi dữ liệu từ DomainUser sang EloquentUser
        $eloquentUser->id = $domainUser->id()->toString();
        $eloquentUser->first_name = $domainUser->fullName()->firstName();
        $eloquentUser->last_name = $domainUser->fullName()->lastName();
        $eloquentUser->email = $domainUser->email()->toString();
        // ...
        
        $eloquentUser->save();
        
        // Dispatch các domain events ra ngoài
        foreach ($domainUser->pullDomainEvents() as $event) {
            // Có thể lưu vào Outbox ở đây
            event($event); 
        }
    }
    
    // ... các phương thức khác ...

    /**
     * Chuyển đổi từ Eloquent model sang Domain model.
     */
    private function toDomain(EloquentUser $eloquentUser): DomainUser
    {
        // Đây là phần phức tạp, cần khởi tạo lại aggregate từ dữ liệu DB
        // Tạm thời để trống để minh họa
        // $user = new DomainUser(...);
        // return $user;
        return unserialize($eloquentUser->domain_state); // Một cách tiếp cận là serialize aggregate
    }
}
```

### 3.2. HTTP Controller

*   **Mô tả:** Lớp "mỏng", chỉ chịu trách nhiệm nhận HTTP request, gọi đến Use Case tương ứng và trả về HTTP response.
*   **Vị trí:** `app/{Context}/Infrastructure/Http/Controllers/`

**Mẫu (`app/OrganizationalStructure/Infrastructure/Http/Controllers/UserController.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function __construct(private UpdateUserProfileUseCase $updateUserProfileUseCase)
    {
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        try {
            $dto = new UpdateUserProfileDTO(
                $request->input('first_name'),
                $request->input('last_name'),
                $request->input('email')
            );
            
            $this->updateUserProfileUseCase->handle($id, $dto);

            return response()->json(['message' => 'User updated successfully.']);

        } catch (UserNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
}
```

### 3.3. Service Provider

*   **Mô tả:** Dùng để đăng ký các binding giữa interface và implementation trong IoC Container của Laravel, dành riêng cho một Bounded Context.
*   **Vị trí:** `app/{Context}/Infrastructure/Providers/`

**Mẫu (`app/OrganizationalStructure/Infrastructure/Providers/OrganizationalStructureServiceProvider.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Infrastructure\Persistence\EloquentUserRepository;

class OrganizationalStructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
```
*Đừng quên đăng ký ServiceProvider này trong `config/app.php`.*

### 3.4. Livewire Components

*   **Mô tả:** Một Livewire component được xem là một "Smart View" trong lớp Infrastructure. Tương tự như Controller, nó là một điểm vào của hệ thống, chịu trách nhiệm quản lý trạng thái UI, nhận tương tác người dùng và điều phối các lệnh gọi đến lớp Application (Use Cases). **Livewire component không được chứa logic nghiệp vụ.**
*   **Vị trí:** `app/{Context}/Infrastructure/Http/Livewire/`

**Mẫu (`app/OrganizationalStructure/Infrastructure/Http/Livewire/UserProfileForm.php`):**

Component này sẽ render một form để người dùng cập nhật thông tin cá nhân.

```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Livewire;

use Livewire\Component;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase; // Giả sử có một use case để đọc dữ liệu
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Application\DTOs\UserDTO; // DTO để hiển thị
use Exception;

final class UserProfileForm extends Component
{
    // Trạng thái của component
    public ?string $userId = null;
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';

    /**
     * Khởi tạo component, tải dữ liệu ban đầu.
     */
    public function mount(FindUserUseCase $findUserUseCase, string $userId): void
    {
        $this->userId = $userId;
        
        // Gọi đến Application layer để lấy dữ liệu đọc (Read Model)
        $userDto = $findUserUseCase->handle($this->userId);

        if ($userDto) {
            $this->firstName = $userDto->firstName;
            $this->lastName = $userDto->lastName;
            $this->email = $userDto->email;
        }
    }

    /**
     * Quy tắc validation của Livewire.
     */
    protected function rules(): array
    {
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email',
        ];
    }
    
    /**
     * Phương thức được gọi khi form được submit.
     */
    public function save(UpdateUserProfileUseCase $updateUserProfileUseCase): void
    {
        // 1. Validate input
        $validatedData = $this->validate();

        try {
            // 2. Tạo DTO để truyền vào Application Layer
            $dto = new UpdateUserProfileDTO(
                $validatedData['firstName'],
                $validatedData['lastName'],
                $validatedData['email']
            );

            // 3. Gọi đến Use Case để thực thi nghiệp vụ
            $updateUserProfileUseCase->handle($this->userId, $dto);

            // 4. Gửi event ra frontend để thông báo thành công (ví dụ: dùng Toast)
            $this->dispatch('user-updated', 'Profile saved successfully!');

        } catch (UserNotFoundException $e) {
            $this->addError('general', 'User not found. Could not save profile.');
        } catch (Exception $e) {
            // Bắt các lỗi khác từ domain hoặc application
            $this->addError('general', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-profile-form');
    }
}
```

**View tương ứng (`resources/views/livewire/user-profile-form.blade.php`):**

```html
<div>
    <form wire:submit.prevent="save">
        @if ($errors->has('general'))
            <div class="alert alert-danger">{{ $errors->first('general') }}</div>
        @endif

        <div>
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" wire:model.defer="firstName">
            @error('firstName') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" wire:model.defer="lastName">
            @error('lastName') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" wire:model.defer="email">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">
            <span wire:loading.remove wire:target="save">Save</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </form>
</div>
```

### 3.5. Domain Exception

*   **Mô tả:** Các exception đặc thù cho domain layer, đại diện cho các lỗi nghiệp vụ.
*   **Vị trí:** `app/{Context}/Domain/Exceptions/`

**Mẫu (`app/OrganizationalStructure/Domain/Exceptions/UserNotFoundException.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a user is not found.
 */
final class UserNotFoundException extends RuntimeException
{
    /**
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        parent::__construct("User not found: {$identifier}");
    }
}
```

**Mẫu (`app/OrganizationalStructure/Domain/Exceptions/UserAlreadyExistsException.php`):**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Exceptions;

use DomainException;

/**
 * Exception thrown when trying to create a user that already exists.
 */
final class UserAlreadyExistsException extends DomainException
{
    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        parent::__construct("User with email {$email} already exists");
    }
}
```

### 3.6. Event Listener

*   **Mô tả:** Các lớp lắng nghe domain events và thực hiện các side effects (ví dụ: gửi email, cập nhật cache).
*   **Vị trí:** `app/{Context}/Infrastructure/Listeners/`

**Mẫu (`app/IdentityAccess/Infrastructure/Listeners/CreateDefaultCredentialsWhenUserWasCreated.php`):**
```php
<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Listeners;

use App\IdentityAccess\Application\UseCases\CreateDefaultCredentialsUseCase;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener that creates default credentials when a user is created.
 */
final class CreateDefaultCredentialsWhenUserWasCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param CreateDefaultCredentialsUseCase $createDefaultCredentialsUseCase
     */
    public function __construct(
        private CreateDefaultCredentialsUseCase $createDefaultCredentialsUseCase
    ) {
    }

    /**
     * Handle the event.
     *
     * @param UserWasCreated $event
     * @return void
     */
    public function handle(UserWasCreated $event): void
    {
        $this->createDefaultCredentialsUseCase->handle(
            $event->userId,
            $event->email
        );
    }

    /**
     * Handle a job failure.
     *
     * @param UserWasCreated $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(UserWasCreated $event, \Throwable $exception): void
    {
        // Log the failure
        \Log::error('Failed to create default credentials', [
            'user_id' => $event->userId,
            'exception' => $exception->getMessage(),
        ]);

        // Optionally, send notification to admin
    }
}
```

**Đăng ký Listener trong `app/Providers/EventServiceProvider.php`:**
```php
<?php

namespace App\Providers;

use App\IdentityAccess\Infrastructure\Listeners\CreateDefaultCredentialsWhenUserWasCreated;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserWasCreated::class => [
            CreateDefaultCredentialsWhenUserWasCreated::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
```

### 3.7. Unit Test Example

*   **Mô tả:** Ví dụ về cách viết unit test cho Domain layer.
*   **Vị trí:** `tests/Unit/`

**Mẫu (`tests/Unit/OrganizationalStructure/Domain/ValueObjects/EmailTest.php`):**
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\ValueObjects;

use App\OrganizationalStructure\Domain\ValueObjects\Email;
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
}
```

**Mẫu Feature Test (`tests/Feature/OrganizationalStructure/Application/UseCases/CreateUserUseCaseTest.php`):**
```php
<?php

declare(strict_types=1);

namespace Tests\Feature\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserAlreadyExistsException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateUserUseCaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user can be created successfully.
     */
    public function test_user_can_be_created_successfully(): void
    {
        // Arrange
        $useCase = app(CreateUserUseCase::class);
        $dto = new CreateUserDTO(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john.doe@example.com',
            password: 'password123'
        );

        // Act
        $user = $useCase->handle($dto);

        // Assert
        $this->assertNotNull($user);
        $this->assertEquals('john.doe@example.com', $user->email()->toString());
        $this->assertEquals('John', $user->fullName()->firstName());
    }

    /**
     * Test that exception is thrown when user already exists.
     */
    public function test_exception_is_thrown_when_user_already_exists(): void
    {
        // Arrange
        $useCase = app(CreateUserUseCase::class);
        $dto = new CreateUserDTO(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john.doe@example.com',
            password: 'password123'
        );

        // Create user first time
        $useCase->handle($dto);

        // Act & Assert
        $this->expectException(UserAlreadyExistsException::class);
        $useCase->handle($dto);
    }
}
```

---

## 4. Shared Kernel (Lõi dùng chung)

Shared Kernel chứa các thành phần được tái sử dụng bởi tất cả các Bounded Context. Đây là nơi chứa các Value Objects, Exceptions, và Interfaces dùng chung.

### 4.1. Shared Value Objects

#### 4.1.1. Email Value Object

*   **Mô tả:** Value Object dùng chung để đại diện cho địa chỉ email, có validation và có thể được sử dụng trong mọi Bounded Context.
*   **Vị trí:** `app/SharedKernel/Domain/ValueObjects/Email.php`

**Mẫu (`app/SharedKernel/Domain/ValueObjects/Email.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use InvalidArgumentException;
use Stringable;

/**
 * Email value object that can be used across all bounded contexts.
 * Immutable and always valid.
 */
final class Email implements Stringable
{
    private string $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param string $value
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = strtolower(trim($value));
    }

    /**
     * Create Email from string.
     *
     * @param string $value
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Validate email format.
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$value}");
        }
    }

    /**
     * Get email value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Get email domain.
     *
     * @return string
     */
    public function domain(): string
    {
        $parts = explode('@', $this->value);
        return $parts[1] ?? '';
    }

    /**
     * Get email local part (before @).
     *
     * @return string
     */
    public function localPart(): string
    {
        $parts = explode('@', $this->value);
        return $parts[0] ?? '';
    }

    /**
     * Compare equality with another Email value object.
     *
     * @param Email $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

#### 4.1.2. Uuid Value Object

*   **Mô tả:** Value Object để đại diện cho UUID (Universally Unique Identifier), đảm bảo tính nhất quán trong việc sử dụng ID.
*   **Vị trí:** `app/SharedKernel/Domain/ValueObjects/Uuid.php`

**Mẫu (`app/SharedKernel/Domain/ValueObjects/Uuid.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * UUID value object that can be used across all bounded contexts.
 * Immutable and always valid.
 */
final class Uuid implements Stringable
{
    private UuidInterface $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param UuidInterface $value
     */
    private function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    /**
     * Create UUID from string.
     *
     * @param string $value
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException("Invalid UUID format: {$value}");
        }

        return new self(RamseyUuid::fromString($value));
    }

    /**
     * Generate a new UUID.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4());
    }

    /**
     * Get UUID value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->toString();
    }

    /**
     * Get UUID value as string (explicit method).
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value->toString();
    }

    /**
     * Compare equality with another Uuid value object.
     *
     * @param Uuid $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value->equals($other->value);
    }
}
```

#### 4.1.3. Timestamp Value Object

*   **Mô tả:** Value Object để đại diện cho thời gian với timezone, đảm bảo tính nhất quán trong việc xử lý thời gian.
*   **Vị trí:** `app/SharedKernel/Domain/ValueObjects/Timestamp.php`

**Mẫu (`app/SharedKernel/Domain/ValueObjects/Timestamp.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Stringable;

/**
 * Timestamp value object that can be used across all bounded contexts.
 * Immutable and always includes timezone information.
 */
final class Timestamp implements Stringable
{
    private DateTimeImmutable $value;

    /**
     * Private constructor to enforce immutability.
     *
     * @param DateTimeImmutable $value
     */
    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * Create Timestamp from DateTimeImmutable.
     *
     * @param DateTimeImmutable $dateTime
     * @return self
     */
    public static function fromDateTime(DateTimeImmutable $dateTime): self
    {
        return new self($dateTime);
    }

    /**
     * Create Timestamp from string.
     *
     * @param string $value
     * @param string|null $timezone
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value, ?string $timezone = null): self
    {
        try {
            $dateTime = new DateTimeImmutable($value, $timezone ? new \DateTimeZone($timezone) : null);
            return new self($dateTime);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Invalid timestamp format: {$value}", 0, $e);
        }
    }

    /**
     * Create Timestamp for current time.
     *
     * @param string|null $timezone
     * @return self
     */
    public static function now(?string $timezone = null): self
    {
        $dateTime = new DateTimeImmutable('now', $timezone ? new \DateTimeZone($timezone) : null);
        return new self($dateTime);
    }

    /**
     * Get timestamp as DateTimeImmutable.
     *
     * @return DateTimeImmutable
     */
    public function toDateTime(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Get timestamp as string in ISO 8601 format.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    /**
     * Get timestamp as string in specific format.
     *
     * @param string $format
     * @return string
     */
    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    /**
     * Compare equality with another Timestamp value object.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    /**
     * Check if this timestamp is before another.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    /**
     * Check if this timestamp is after another.
     *
     * @param Timestamp $other
     * @return bool
     */
    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }
}
```

### 4.2. Shared Exceptions

#### 4.2.1. DomainException

*   **Mô tả:** Exception cơ bản cho domain layer, tất cả các domain exceptions nên extend từ class này.
*   **Vị trí:** `app/SharedKernel/Domain/Exceptions/DomainException.php`

**Mẫu (`app/SharedKernel/Domain/Exceptions/DomainException.php`):**
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

#### 4.2.2. EntityNotFoundException

*   **Mô tả:** Exception được throw khi không tìm thấy một entity trong domain.
*   **Vị trí:** `app/SharedKernel/Domain/Exceptions/EntityNotFoundException.php`

**Mẫu (`app/SharedKernel/Domain/Exceptions/EntityNotFoundException.php`):**
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

#### 4.2.3. InvalidArgumentException

*   **Mô tả:** Exception được throw khi tham số không hợp lệ.
*   **Vị trí:** `app/SharedKernel/Domain/Exceptions/InvalidArgumentException.php`

**Mẫu (`app/SharedKernel/Domain/Exceptions/InvalidArgumentException.php`):**
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

### 4.3. Shared Interfaces

#### 4.3.1. EventDispatcherInterface

*   **Mô tả:** Interface để dispatch domain events, cho phép dễ dàng mock trong testing.
*   **Vị trí:** `app/SharedKernel/Domain/Interfaces/EventDispatcherInterface.php`

**Mẫu (`app/SharedKernel/Domain/Interfaces/EventDispatcherInterface.php`):**
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

**Implementation (`app/SharedKernel/Infrastructure/EventDispatcher/LaravelEventDispatcher.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\EventDispatcher;

use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Laravel implementation of EventDispatcherInterface.
 */
final class LaravelEventDispatcher implements EventDispatcherInterface
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        private Dispatcher $dispatcher
    ) {
    }

    /**
     * Dispatch a domain event.
     *
     * @param object $event
     * @return void
     */
    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    /**
     * Dispatch multiple domain events.
     *
     * @param array<object> $events
     * @return void
     */
    public function dispatchMany(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
```

#### 4.3.2. ClockInterface

*   **Mô tả:** Interface để lấy thời gian hiện tại, hữu ích cho testing và đảm bảo tính nhất quán về thời gian.
*   **Vị trí:** `app/SharedKernel/Domain/Interfaces/ClockInterface.php`

**Mẫu (`app/SharedKernel/Domain/Interfaces/ClockInterface.php`):**
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

**Implementation (`app/SharedKernel/Infrastructure/Clock/SystemClock.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Clock;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * System clock implementation.
 */
final class SystemClock implements ClockInterface
{
    /**
     * Get current timestamp.
     *
     * @param string|null $timezone
     * @return Timestamp
     */
    public function now(?string $timezone = null): Timestamp
    {
        return Timestamp::now($timezone);
    }
}
```

**Test Implementation (`app/SharedKernel/Infrastructure/Clock/FixedClock.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Clock;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\ValueObjects\Timestamp;

/**
 * Fixed clock implementation for testing.
 * Always returns the same timestamp.
 */
final class FixedClock implements ClockInterface
{
    private Timestamp $fixedTime;

    /**
     * @param Timestamp $fixedTime
     */
    public function __construct(Timestamp $fixedTime)
    {
        $this->fixedTime = $fixedTime;
    }

    /**
     * Get fixed timestamp.
     *
     * @param string|null $timezone
     * @return Timestamp
     */
    public function now(?string $timezone = null): Timestamp
    {
        return $this->fixedTime;
    }
}
```

### 4.4. Service Provider cho Shared Kernel

*   **Mô tả:** Service Provider để đăng ký các bindings cho Shared Kernel.
*   **Vị trí:** `app/SharedKernel/Infrastructure/Providers/SharedKernelServiceProvider.php`

**Mẫu (`app/SharedKernel/Infrastructure/Providers/SharedKernelServiceProvider.php`):**
```php
<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Providers;

use App\SharedKernel\Domain\Interfaces\ClockInterface;
use App\SharedKernel\Domain\Interfaces\EventDispatcherInterface;
use App\SharedKernel\Infrastructure\Clock\SystemClock;
use App\SharedKernel\Infrastructure\EventDispatcher\LaravelEventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for Shared Kernel.
 */
class SharedKernelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind EventDispatcherInterface
        $this->app->singleton(EventDispatcherInterface::class, function ($app) {
            return new LaravelEventDispatcher($app->make(Dispatcher::class));
        });

        // Bind ClockInterface
        $this->app->singleton(ClockInterface::class, SystemClock::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
```

### 4.5. Ví dụ sử dụng Shared Kernel trong Bounded Context

**Ví dụ sử dụng Email trong Domain Aggregate:**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Aggregates;

use App\SharedKernel\Domain\ValueObjects\Email;

final class User
{
    private Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
```

**Ví dụ sử dụng Uuid trong Repository:**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Repositories;

use App\SharedKernel\Domain\ValueObjects\Uuid;

interface UserRepositoryInterface
{
    public function nextIdentity(): Uuid;
    public function findById(Uuid $id): ?User;
}
```

**Ví dụ sử dụng ClockInterface trong Use Case:**
```php
<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\SharedKernel\Domain\Interfaces\ClockInterface;

final class CreateUserUseCase
{
    public function __construct(
        private ClockInterface $clock
    ) {
    }

    public function handle(CreateUserDTO $dto): User
    {
        $createdAt = $this->clock->now();
        // Use $createdAt in business logic
    }
}
```
