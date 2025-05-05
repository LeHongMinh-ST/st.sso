<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class Detail extends Component
{
    public User $user;

    public function render()
    {
        return view('livewire.user.detail');
    }

    public function mount($user): void
    {
        $this->user = $user;
    }

    #[On('deleteUser')]
    public function delete()
    {
        if (!auth()->user()->can('delete', $this->user)) {
            session()->flash('error', 'Bạn không có quyền xóa người dùng!');
            return;
        }

        $this->user->delete();
        session()->flash('success', 'Xoá thành công!');
        return redirect()->route('user.index');
    }

    public function openDeleteModal(): void
    {
        if (!auth()->user()->can('delete', $this->user)) {
            return;
        }
        $this->dispatch('onOpenDeleteModal');
    }

    public function resetPassword(): void
    {
        if (!auth()->user()->can('resetPassword', $this->user)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền đặt lại mật khẩu!');
            return;
        }

        try {
            $this->user->update([
                'password' => Hash::make('password'),
                'is_change_password' => false
            ]);

            session()->flash('success', 'Reset mật khẩu thành công!');
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Reset mật khẩu thất bại!');
        }
    }

    public function openResetPasswordModal(): void
    {
        $this->dispatch('onOpenResetPasswordModal');
    }
}
