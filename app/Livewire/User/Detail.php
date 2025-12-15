<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
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
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->user)) {
            session()->flash('error', 'Bạn không có quyền xóa người dùng!');
            return;
        }

        $this->user->delete();
        session()->flash('success', 'Xoá thành công!');
        return redirect()->route('user.index');
    }

    public function openDeleteModal(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->user)) {
            return;
        }
        $this->dispatch('onOpenDeleteModal');
    }

    public function resetPassword(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->can($currentUser, $this->user, 'resetPassword')) {
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
