<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\Models\Role;
use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public Role $role;

    public function render()
    {
        $permissions = $this->role->permissions->groupBy(fn ($item) => $item->group ? $item->group->name : 'Other');

        return view('livewire.role.detail', [
            'permissions' => $permissions
        ]);
    }

    public function mount($role): void
    {
        $this->role = $role;
    }

    #[On('deleteRole')]
    public function delete()
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->role)) {
            session()->flash('error', 'Bạn không có quyền xóa vai trò!');
            return redirect()->route('role.show', $this->role->id);
        }

        if ('super-admin' === $this->role->name) {
            session()->flash('error', 'Không thể xóa vai trò Super Admin!');
            return redirect()->route('role.show', $this->role->id);
        }

        // Detach all permissions before deleting the role
        $this->role->permissions()->detach();

        // Delete the role
        $this->role->delete();
        session()->flash('success', 'Xoá vai trò thành công!');
        return redirect()->route('role.index');
    }

    public function openDeleteModal(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->role) || 'super-admin' === $this->role->name) {
            return;
        }

        $this->dispatch('onOpenDeleteModal');
    }

    /**
     * Get PolicyAuthorizationService instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return PolicyAuthorizationServiceInterface
     */
    private function getPolicyAuthorizationService(): PolicyAuthorizationServiceInterface
    {
        return app(PolicyAuthorizationServiceInterface::class);
    }
}
