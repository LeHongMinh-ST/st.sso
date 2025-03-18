<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;

class Detail extends Component
{
    public User $user;
 
    protected $listeners = [
        'deleteUser' => 'delete',
    ];

    public function render()
    {
        return view('livewire.user.detail');
    }

    public function mount($user)
    {   
        $this->user = $user;    
    }

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