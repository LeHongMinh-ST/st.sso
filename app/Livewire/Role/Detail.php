<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Detail extends Component
{
    public Role $role;

    public function render()
    {
        $permissions = $this->role->permissions->groupBy('group');

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
        if (!auth()->user()->can('role.delete')) {
            session()->flash('error', 'Bạn không có quyền xóa vai trò!');
            return redirect()->route('role.show', $this->role->id);
        }

        if ('super-admin' === $this->role->name) {
            session()->flash('error', 'Không thể xóa vai trò Super Admin!');
            return redirect()->route('role.show', $this->role->id);
        }

        $this->role->delete();
        session()->flash('success', 'Xoá vai trò thành công!');
        return redirect()->route('role.index');
    }

    public function openDeleteModal(): void
    {
        if (!auth()->user()->can('role.delete') || 'super-admin' === $this->role->name) {
            return;
        }

        $this->dispatch('onOpenDeleteModal');
    }
}
