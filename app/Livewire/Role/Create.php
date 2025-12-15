<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\IdentityAccess\Application\DTOs\AssignPermissionToRoleDTO;
use App\IdentityAccess\Application\DTOs\CreateRoleDTO;
use App\IdentityAccess\Application\UseCases\AssignPermissionToRoleUseCase;
use App\IdentityAccess\Application\UseCases\CreateRoleUseCase;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;
use Throwable;

/**
 * Livewire component for creating a role.
 *
 * Note: Uses app() helper for dependency injection as per Livewire convention.
 */
class Create extends Component
{
    #[Validate(as: 'tên vai trò')]
    public string $name = '';

    public array $selectedPermissions = [];

    private bool $isLoading = false;

    public function render()
    {
        $permissions = Permission::all()->groupBy(fn ($item) => $item->group ? $item->group->name : 'Other');

        return view('livewire.role.create', [
            'permissions' => $permissions
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:roles,name',
            'selectedPermissions' => 'array',
        ];
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('role.create')) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền tạo vai trò!');
            return;
        }

        try {
            $this->isLoading = true;
            $this->validate();

            // Use CreateRoleUseCase
            $createRoleUseCase = $this->getCreateRoleUseCase();
            $dto = new CreateRoleDTO(
                name: $this->name,
                displayName: $this->name,
                description: '',
            );

            $role = $createRoleUseCase->execute($dto);

            // Assign permissions if selected
            if (!empty($this->selectedPermissions)) {
                $this->assignPermissionsToRole($role->id()->toString(), $this->selectedPermissions);
            }

            session()->flash('success', 'Tạo mới vai trò thành công!');

            // Get integer ID for redirect
            $roleId = $this->getIntegerIdFromUuid('roles', $role->id()->toString());
            return redirect()->route('role.show', $roleId);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới vai trò thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Assign permissions to role.
     *
     * @param string $roleUuid Role UUID
     * @param array<string> $permissionCodes Permission codes
     * @return void
     */
    private function assignPermissionsToRole(string $roleUuid, array $permissionCodes): void
    {
        $assignPermissionUseCase = $this->getAssignPermissionToRoleUseCase();
        $permissionRepository = $this->getPermissionRepository();

        foreach ($permissionCodes as $permissionCode) {
            $permission = $permissionRepository->findByCode($permissionCode);
            if (null !== $permission) {
                $dto = new AssignPermissionToRoleDTO(
                    roleId: $roleUuid,
                    permissionId: $permission->id()->toString(),
                );
                $assignPermissionUseCase->execute($dto);
            }
        }
    }

    /**
     * Get CreateRoleUseCase instance.
     * Uses app() helper as per Livewire convention.
     *
     * @return CreateRoleUseCase
     */
    private function getCreateRoleUseCase(): CreateRoleUseCase
    {
        return app(CreateRoleUseCase::class);
    }

    /**
     * Get AssignPermissionToRoleUseCase instance.
     *
     * @return AssignPermissionToRoleUseCase
     */
    private function getAssignPermissionToRoleUseCase(): AssignPermissionToRoleUseCase
    {
        return app(AssignPermissionToRoleUseCase::class);
    }

    /**
     * Get PermissionRepositoryInterface instance.
     *
     * @return PermissionRepositoryInterface
     */
    private function getPermissionRepository(): PermissionRepositoryInterface
    {
        return app(PermissionRepositoryInterface::class);
    }

    /**
     * Get integer ID from UUID.
     *
     * @param string $table Table name
     * @param string $uuid UUID string
     * @return int Integer ID
     */
    private function getIntegerIdFromUuid(string $table, string $uuid): int
    {
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn($table, 'uuid');

        if ($hasUuidColumn) {
            $id = \Illuminate\Support\Facades\DB::table($table)->where('uuid', $uuid)->value('id');
            if (null !== $id) {
                return $id;
            }
        }

        // Fallback: try to find by deterministic UUID
        $records = \Illuminate\Support\Facades\DB::table($table)->select('id')->get();
        foreach ($records as $record) {
            $generatedUuid = $this->generateDeterministicUuid($table, $record->id);
            if ($generatedUuid === $uuid) {
                return $record->id;
            }
        }

        throw new RuntimeException("Cannot find integer ID for UUID: {$uuid}");
    }

    /**
     * Generate deterministic UUID from integer ID.
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
