<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Throwable;

class Roles extends Component
{
    public User $user;
    public array $selectedRoles = [];
    public array $directPermissions = [];
    public array $availablePermissions = [];

    private bool $isLoading = false;

    public function render()
    {
        $roles = Role::all();

        return view('livewire.user.roles', [
            'roles' => $roles
        ]);
    }

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
        $this->directPermissions = $this->user->getDirectPermissions()->pluck('name')->toArray();

        // Lấy tất cả quyền có sẵn
        $this->availablePermissions = \Spatie\Permission\Models\Permission::all()
            ->groupBy('group')
            ->toArray();
    }

    public function updateRoles()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('role.assign_users')) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền gán vai trò cho người dùng!');
            return;
        }

        try {
            $this->isLoading = true;

            // Cập nhật vai trò
            $roles = Role::whereIn('id', $this->selectedRoles)->get();
            $this->user->syncRoles($roles);

            // Cập nhật quyền trực tiếp
            $this->user->syncPermissions($this->directPermissions);

            session()->flash('success', 'Cập nhật vai trò và quyền thành công!');
            return redirect()->route('user.show', $this->user->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật vai trò và quyền thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
