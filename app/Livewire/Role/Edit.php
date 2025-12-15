<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\IdentityAccess\Application\DTOs\AssignPermissionToRoleDTO;
use App\IdentityAccess\Application\DTOs\RemovePermissionFromRoleDTO;
use App\IdentityAccess\Application\Services\AuthorizationService;
use App\IdentityAccess\Application\UseCases\AssignPermissionToRoleUseCase;
use App\IdentityAccess\Application\UseCases\RemovePermissionFromRoleUseCase;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\ValueObjects\RoleId;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;
use Throwable;

/**
 * Livewire component for editing a role.
 *
 * Note: Uses app() helper for dependency injection as per Livewire convention.
 */
class Edit extends Component
{
    public Role $role;

    #[Validate(as: 'tên vai trò')]
    public string $name = '';

    public array $selectedPermissions = [];

    private bool $isLoading = false;

    public function render()
    {
        $permissions = Permission::all()->groupBy(fn ($item) => $item->group ? $item->group->name : 'Other');

        return view('livewire.role.edit', [
            'permissions' => $permissions
        ]);
    }

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('code')->toArray();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:roles,name,' . $this->role->id,
            'selectedPermissions' => 'array',
        ];
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getAuthorizationService()->can($currentUser, 'role.edit')) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền chỉnh sửa vai trò!');
            return;
        }

        if ('super-admin' === $this->role->name) {
            $this->dispatch('alert', type: 'error', message: 'Không thể chỉnh sửa vai trò Super Admin!');
            return;
        }

        try {
            $this->isLoading = true;
            $this->validate();

            // Get role UUID
            $roleUuid = $this->getRoleUuid($this->role->id);
            $roleRepository = $this->getRoleRepository();
            $domainRole = $roleRepository->findById(RoleId::fromString($roleUuid));

            if (null === $domainRole) {
                throw new RuntimeException('Role not found');
            }

            // Update role name (if changed)
            if ($domainRole->name() !== $this->name) {
                // Note: Role aggregate doesn't have updateName method yet
                // For now, we'll update via Eloquent and reload
                $this->role->update([
                    'name' => $this->name,
                    'display_name' => $this->name,
                ]);
                $domainRole = $roleRepository->findById(RoleId::fromString($roleUuid));
            }

            // Get current permission codes
            $currentPermissionCodes = $domainRole->permissionIds();

            // Get selected permission UUIDs
            $selectedPermissionUuids = [];
            $permissionRepository = $this->getPermissionRepository();
            foreach ($this->selectedPermissions as $permissionCode) {
                $permission = $permissionRepository->findByCode($permissionCode);
                if (null !== $permission) {
                    $selectedPermissionUuids[] = $permission->id()->toString();
                }
            }

            // Remove permissions that are no longer selected
            $permissionsToRemove = array_diff($currentPermissionCodes, $selectedPermissionUuids);
            $removePermissionUseCase = $this->getRemovePermissionFromRoleUseCase();
            foreach ($permissionsToRemove as $permissionUuid) {
                $removeDto = new RemovePermissionFromRoleDTO(
                    roleId: $roleUuid,
                    permissionId: $permissionUuid,
                );
                $removePermissionUseCase->execute($removeDto);
            }

            // Reload role after removing permissions
            $domainRole = $roleRepository->findById(RoleId::fromString($roleUuid));

            // Add new permissions
            $permissionsToAdd = array_diff($selectedPermissionUuids, $currentPermissionCodes);
            $assignPermissionUseCase = $this->getAssignPermissionToRoleUseCase();
            foreach ($permissionsToAdd as $permissionUuid) {
                $dto = new AssignPermissionToRoleDTO(
                    roleId: $roleUuid,
                    permissionId: $permissionUuid,
                );
                $assignPermissionUseCase->execute($dto);
            }

            // Save role
            $roleRepository->save($domainRole);

            session()->flash('success', 'Cập nhật vai trò thành công!');
            return redirect()->route('role.show', $this->role->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật vai trò thất bại!');
        } finally {
            $this->isLoading = false;
        }
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
     * Get RemovePermissionFromRoleUseCase instance.
     *
     * @return RemovePermissionFromRoleUseCase
     */
    private function getRemovePermissionFromRoleUseCase(): RemovePermissionFromRoleUseCase
    {
        return app(RemovePermissionFromRoleUseCase::class);
    }

    /**
     * Get RoleRepositoryInterface instance.
     *
     * @return RoleRepositoryInterface
     */
    private function getRoleRepository(): RoleRepositoryInterface
    {
        return app(RoleRepositoryInterface::class);
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
     * Get role UUID from integer ID.
     *
     * @param int $roleId Integer role ID
     * @return string Role UUID
     */
    private function getRoleUuid(int $roleId): string
    {
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('roles', 'uuid');

        if ($hasUuidColumn) {
            $uuid = \Illuminate\Support\Facades\DB::table('roles')->where('id', $roleId)->value('uuid');
            if (null !== $uuid) {
                return $uuid;
            }
        }

        return $this->generateDeterministicUuid('roles', $roleId);
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

    /**
     * Get AuthorizationService instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return AuthorizationService
     */
    private function getAuthorizationService(): AuthorizationService
    {
        return app(AuthorizationService::class);
    }
}
