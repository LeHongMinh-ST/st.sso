<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class Roles extends Component
{
    public User $user;
    public array $selectedRoles = [];

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
        $this->selectedRoles = $this->user->roles()->pluck('roles.id')->toArray();
    }

    public function updateRoles()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('viewAny', Role::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền gán vai trò cho người dùng!');
            return;
        }

        try {
            $this->isLoading = true;

            // Detach all current roles
            $this->user->roles()->detach();

            // Attach selected roles
            if (!empty($this->selectedRoles)) {
                foreach ($this->selectedRoles as $roleId) {
                    $this->user->roles()->attach($roleId);
                }
            }

            session()->flash('success', 'Cập nhật vai trò thành công!');
            return redirect()->route('user.show', $this->user->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật vai trò thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
