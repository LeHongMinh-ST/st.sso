<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Index extends Component
{

    public $tab = 'profile';

    public function render()
    {
        $tabs = [
            'profile' => 'Thông tin tài khoản',
            'password' => 'Mật khẩu'
        ];

        return view('livewire.profile.index', [
            'tabs' => $tabs
        ]);
    }

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }
}
