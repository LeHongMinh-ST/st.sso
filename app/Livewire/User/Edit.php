<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Enums\Role;
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Application\UseCases\AssignUserToDepartmentUseCase;
use App\OrganizationalStructure\Application\UseCases\AssignUserToFacultyUseCase;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\SharedKernel\Domain\Enums\Status;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;
use Throwable;

/**
 * Livewire component for editing a user.
 * Refactored to use DDD Use Cases for OrganizationalStructure context.
 * Password and Role handling is temporary until IdentityAccess context (Phase 3).
 */
class Edit extends Component
{
    public User $user;

    #[Validate(as: 'tên người dùng')]
    public string $user_name;

    #[Validate(as: 'tên')]
    public string $first_name;

    #[Validate(as: 'họ')]
    public string $last_name;

    #[Validate(as: 'email')]
    public string $email;

    #[Validate(as: 'số điện thoại')]
    public string|null $phone = '';

    public Role $role = Role::Normal;

    #[Validate(as: 'mã người dùng')]
    public string $code = '';

    public int|null|string $department_id = null;

    public int|null|string $faculty_id = null;

    public Status $status = Status::Active;

    public bool $is_only_login_ms = false;

    private bool $isLoading = false;

    private ?string $userUuid = null;

    public function render()
    {
        $faculties = Faculty::all();

        return view('livewire.user.edit', [
            'faculties' => $faculties,
        ]);
    }

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->user_name = $user->user_name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->role;
        $this->code = $user->code;
        $this->department_id = $user->department_id;
        $this->faculty_id = $user->faculty_id;
        $this->is_only_login_ms = (bool) $user->is_only_login_ms;

        // Get user UUID
        $this->userUuid = $user->uuid ?? $this->generateDeterministicUuid('users', $user->id);
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name,' . $this->user->id,
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'code' => 'nullable|max:255|unique:users,code,' . $this->user->id,
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'is_only_login_ms' => 'nullable|boolean',
        ];

        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code,' . $this->user->id;
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'nullable|max:255|unique:users,code,' . $this->user->id;
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

        if (!auth()->user()->can('update', $this->user)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền chỉnh sửa người dùng!');
            return;
        }

        $this->validate();

        try {
            $this->isLoading = true;

            if (null === $this->userUuid) {
                throw new RuntimeException('User UUID not found');
            }

            // Update user profile using Use Case
            $updateDTO = UpdateUserProfileDTO::fromArray([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            $this->getUpdateUserProfileUseCase()->execute($this->userUuid, $updateDTO);

            // Assign to faculty if changed
            if (null !== $this->faculty_id) {
                $facultyUuid = $this->getFacultyUuid($this->faculty_id);
                if (null !== $facultyUuid) {
                    $this->getAssignUserToFacultyUseCase()->execute($this->userUuid, $facultyUuid);
                }
            }

            // Assign to department if changed
            if (null !== $this->department_id) {
                $departmentUuid = $this->getDepartmentUuid($this->department_id);
                if (null !== $departmentUuid) {
                    $this->getAssignUserToDepartmentUseCase()->execute($this->userUuid, $departmentUuid);
                }
            }

            // Handle role and other fields (temporary until Phase 3)
            $this->handleRoleAndOtherFields();

            session()->flash('success', 'Cập nhật thành công!');
            return redirect()->route('user.show', $this->user->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật thất bại: ' . $th->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function toggleStatus(): void
    {
        $this->status = Status::Active === $this->status
            ? Status::Inactive
            : Status::Active;
    }

    public function toggleIsOnlyLoginMs(): void
    {
        $this->is_only_login_ms = !$this->is_only_login_ms;
    }

    /**
     * Get UpdateUserProfileUseCase instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return UpdateUserProfileUseCase
     */
    private function getUpdateUserProfileUseCase(): UpdateUserProfileUseCase
    {
        return app(UpdateUserProfileUseCase::class);
    }

    /**
     * Get AssignUserToFacultyUseCase instance.
     *
     * @return AssignUserToFacultyUseCase
     */
    private function getAssignUserToFacultyUseCase(): AssignUserToFacultyUseCase
    {
        return app(AssignUserToFacultyUseCase::class);
    }

    /**
     * Get AssignUserToDepartmentUseCase instance.
     *
     * @return AssignUserToDepartmentUseCase
     */
    private function getAssignUserToDepartmentUseCase(): AssignUserToDepartmentUseCase
    {
        return app(AssignUserToDepartmentUseCase::class);
    }

    /**
     * Handle role and other fields that are not part of OrganizationalStructure context.
     * This is temporary until IdentityAccess context is implemented (Phase 3).
     *
     * @return void
     */
    private function handleRoleAndOtherFields(): void
    {
        $this->user->update([
            'user_name' => $this->user_name,
            'role' => $this->role->value,
            'code' => $this->code,
            'status' => $this->status->value,
            'is_only_login_ms' => $this->is_only_login_ms,
        ]);
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

        return $faculty->uuid ?? $this->generateDeterministicUuid('faculties', (int) $facultyId);
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

        $department = \App\OrganizationalStructure\Infrastructure\Eloquent\Department::find($departmentId);
        if (null === $department) {
            return null;
        }

        return $department->uuid ?? $this->generateDeterministicUuid('departments', (int) $departmentId);
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
