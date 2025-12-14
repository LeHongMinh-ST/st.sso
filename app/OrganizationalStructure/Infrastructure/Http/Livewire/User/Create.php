<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Livewire\User;

use App\Enums\Role;
use App\Models\Faculty;
use App\Models\User;
use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\UseCases\AssignUserToDepartmentUseCase;
use App\OrganizationalStructure\Application\UseCases\AssignUserToFacultyUseCase;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;
use Throwable;

/**
 * Livewire component for creating a new user.
 * Refactored to use DDD Use Cases for OrganizationalStructure context.
 * Password and Role handling is temporary until IdentityAccess context (Phase 3).
 */
class Create extends Component
{
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

    public bool $is_only_login_ms = false;

    public int|null|string $department_id = null;

    public int|null|string $faculty_id = null;

    private bool $isLoading = false;

    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly AssignUserToFacultyUseCase $assignUserToFacultyUseCase,
        private readonly AssignUserToDepartmentUseCase $assignUserToDepartmentUseCase,
        private readonly UserRepositoryInterface $userRepository,
        private readonly FacultyRepositoryInterface $facultyRepository,
    ) {
        parent::__construct();
    }

    public function render()
    {
        $faculties = Faculty::all();

        return view('livewire.user.create', [
            'faculties' => $faculties,
        ]);
    }

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'department_id' => 'nullable',
            'faculty_id' => 'nullable',
            'is_only_login_ms' => 'nullable|boolean',
        ];

        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code';
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'nullable|max:255|unique:users,code';
        } else {
            $rules['code'] = 'nullable|max:255';
        }

        return $rules;
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('create', User::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền tạo người dùng!');
            return;
        }

        $this->validate();

        try {
            $this->isLoading = true;

            // Convert faculty_id and department_id to UUID if needed
            $facultyUuid = $this->getFacultyUuid($this->faculty_id);
            $departmentUuid = $this->getDepartmentUuid($this->department_id);

            // Create user using Use Case (OrganizationalStructure part)
            $createDTO = CreateUserDTO::fromArray([
                'user_name' => $this->user_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'user_code' => $this->code ?: null,
                'phone' => $this->phone ?: null,
                'faculty_id' => $facultyUuid,
                'department_id' => $departmentUuid,
            ]);

            $user = $this->createUserUseCase->execute($createDTO);

            // Handle password and role (temporary until Phase 3)
            $this->handlePasswordAndRole($user, $this->role, $this->is_only_login_ms);

            session()->flash('success', 'Tạo mới thành công!');
            return redirect()->route('user.show', $this->getUserIntegerId($user->id()->toString()));
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại: ' . $th->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleIsOnlyLoginMs(): void
    {
        $this->is_only_login_ms = !$this->is_only_login_ms;
    }

    /**
     * Get faculty UUID from integer ID.
     *
     * @param int|string|null $facultyId Faculty integer ID
     * @return string|null Faculty UUID
     */
    private function getFacultyUuid(int|string|null $facultyId): ?string
    {
        if (null === $facultyId) {
            return null;
        }

        $faculty = Faculty::find($facultyId);
        if (null === $faculty) {
            return null;
        }

        // If UUID column exists, use it
        if (null !== $faculty->uuid) {
            return $faculty->uuid;
        }

        // Otherwise, convert integer ID to deterministic UUID
        return $this->generateDeterministicUuid('faculties', (int) $facultyId);
    }

    /**
     * Get department UUID from integer ID.
     *
     * @param int|string|null $departmentId Department integer ID
     * @return string|null Department UUID
     */
    private function getDepartmentUuid(int|string|null $departmentId): ?string
    {
        if (null === $departmentId) {
            return null;
        }

        $department = \App\Models\Department::find($departmentId);
        if (null === $department) {
            return null;
        }

        // If UUID column exists, use it
        if (null !== $department->uuid) {
            return $department->uuid;
        }

        // Otherwise, convert integer ID to deterministic UUID
        return $this->generateDeterministicUuid('departments', (int) $departmentId);
    }

    /**
     * Get user integer ID from UUID.
     *
     * @param string $userUuid User UUID
     * @return int User integer ID
     */
    private function getUserIntegerId(string $userUuid): int
    {
        $user = User::where('uuid', $userUuid)->first();
        if (null !== $user) {
            return $user->id;
        }

        // Fallback: try to find by deterministic UUID
        // This is temporary until UUID migration is complete
        $users = User::all();
        foreach ($users as $u) {
            $generatedUuid = $this->generateDeterministicUuid('users', $u->id);
            if ($generatedUuid === $userUuid) {
                return $u->id;
            }
        }

        throw new RuntimeException("User with UUID {$userUuid} not found");
    }

    /**
     * Handle password and role for created user.
     * This is temporary until IdentityAccess context is implemented (Phase 3).
     *
     * @param \App\OrganizationalStructure\Domain\Aggregates\User $user User aggregate
     * @param Role $role User role
     * @param bool $isOnlyLoginMs Is only login MS flag
     * @return void
     */
    private function handlePasswordAndRole(
        \App\OrganizationalStructure\Domain\Aggregates\User $user,
        Role $role,
        bool $isOnlyLoginMs,
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
                'is_only_login_ms' => $isOnlyLoginMs,
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
