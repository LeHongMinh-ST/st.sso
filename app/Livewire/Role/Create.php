<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class Create extends Component
{
    #[Validate(as: 'tên vai trò')]
    public string $name = '';

    public array $selectedPermissions = [];

    private bool $isLoading = false;

    public function render()
    {
        $permissions = Permission::all()->groupBy('group');

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
        try {
            $this->isLoading = true;
            $this->validate();

            $role = Role::create([
                'name' => $this->name,
            ]);

            if (!empty($this->selectedPermissions)) {
                $role->syncPermissions($this->selectedPermissions);
            }

            session()->flash('success', 'Tạo mới vai trò thành công!');
            return redirect()->route('role.show', $role->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới vai trò thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
