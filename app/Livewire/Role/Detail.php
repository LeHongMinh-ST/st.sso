<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\Models\Role;
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
        if (!auth()->user()->can('delete', $this->role)) {
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
        if (!auth()->user()->can('delete', $this->role) || 'super-admin' === $this->role->name) {
            return;
        }

        $this->dispatch('onOpenDeleteModal');
    }
}
