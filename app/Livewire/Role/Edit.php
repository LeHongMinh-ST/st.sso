<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

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

        if (!auth()->user()->can('role.edit')) {
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

            $this->role->update([
                'name' => $this->name,
                'display_name' => $this->name,
            ]);

            // Detach all current permissions
            $this->role->permissions()->detach();

            // Attach selected permissions
            if (!empty($this->selectedPermissions)) {
                $permissions = Permission::whereIn('code', $this->selectedPermissions)->get();
                foreach ($permissions as $permission) {
                    $this->role->permissions()->attach($permission->id);
                }
            }

            session()->flash('success', 'Cập nhật vai trò thành công!');
            return redirect()->route('role.show', $this->role->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật vai trò thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
