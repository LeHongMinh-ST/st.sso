<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

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
        $this->user->delete();
        session()->flash('success', 'Xoá thành công!');
        return redirect()->route('user.index');
    }

    public function openDeleteModal(): void
    {
        $this->dispatch('onOpenDeleteModal');
    }
}
