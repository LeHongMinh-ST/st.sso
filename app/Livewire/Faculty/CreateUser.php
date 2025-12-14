<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Enums\Role;
use App\Models\Faculty;
use App\Models\User;
use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

/**
 * Livewire component for creating a user within a faculty.
 * Refactored to use DDD Use Cases for OrganizationalStructure context.
 * Password and Role handling is temporary until IdentityAccess context (Phase 3).
 */
class CreateUser extends Component
{
    public Faculty $faculty;

    #[Validate(as: 'tên người dùng')]
    public string $user_name;

    #[Validate(as: 'tên')]
    public string $first_name;

    #[Validate(as: 'họ')]
    public string $last_name;

    #[Validate(as: 'email')]
    public string $email;

    #[Validate(as: 'số điện thoại')]
    public string $phone = '';

    public Role $role = Role::Officer;

    #[Validate(as: 'mã người dùng')]
    public string $code = '';

    private bool $isLoading = false;

    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
    ) {
        parent::__construct();
    }

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function render()
    {
        return view('livewire.faculty.create-user');
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:20',
        ];

        // Mã sinh viên là bắt buộc nếu loại tài khoản là sinh viên
        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code';
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'nullable|max:255|unique:users,code';
        } else {
            $rules['code'] = 'nullable|max:255';
        }

        return $rules;
    }

    public function mount(Faculty $faculty): void
    {
        $this->faculty = $faculty;
    }

    public function submit(): void
    {
        if ($this->isLoading) {
            return;
        }
        $this->validate();

        try {
            $this->isLoading = true;

            // Get faculty UUID
            $facultyUuid = $this->faculty->uuid ?? $this->generateDeterministicUuid('faculties', $this->faculty->id);

            // Create user using Use Case
            $createDTO = CreateUserDTO::fromArray([
                'user_name' => $this->user_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'user_code' => $this->code ?: null,
                'phone' => $this->phone ?: null,
                'faculty_id' => $facultyUuid,
            ]);

            $user = $this->createUserUseCase->execute($createDTO);

            // Handle password and role (temporary until Phase 3)
            $this->handlePasswordAndRole($user, $this->role);

            session()->flash('success', 'Tạo mới người dùng thành công!');
            $this->reset(['user_name', 'first_name', 'last_name', 'email', 'phone', 'code']);
            $this->role = Role::Normal;
            $this->dispatch('userCreated');
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại: ' . $th->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Handle password and role for created user.
     * This is temporary until IdentityAccess context is implemented (Phase 3).
     *
     * @param \App\OrganizationalStructure\Domain\Aggregates\User $user User aggregate
     * @param Role $role User role
     * @return void
     */
    private function handlePasswordAndRole(
        \App\OrganizationalStructure\Domain\Aggregates\User $user,
        Role $role,
    ): void {
        // Find user by UUID and update password/role
        $eloquentUser = User::where('uuid', $user->id()->toString())->first();
        if (null === $eloquentUser) {
            // Fallback: find by email
            $eloquentUser = User::where('email', (string) $user->email())->first();
        }

        if (null !== $eloquentUser) {
            $eloquentUser->update([
                'password' => Hash::make('password'),
                'role' => $role->value,
                'is_change_password' => false,
            ]);
        }
    }

    /**
     * Generate deterministic UUID from integer ID.
     * Temporary helper until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }
}
