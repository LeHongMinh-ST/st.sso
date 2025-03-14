<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $tab = 'profile';

    public function render()
    {
        return view('livewire.profile.index');
    }

    public function mount(): void
    {
        $this->checkChangePassword();
    }

    public function selectTab($tab): void
    {
        if ($this->checkChangePassword()) {
            $this->tab = $tab;
        }
    }

    private function checkChangePassword()
    {
        $user = Auth::user();

        if ($user && !$user->is_change_password) {
            $this->tab = 'password';
        }
        return $user->is_change_password;
    }
}
