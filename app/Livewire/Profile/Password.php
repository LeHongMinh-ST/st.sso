<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Password extends Component
{
    #[Validate(as: 'mật khẩu')]
    public string $password = '';

    #[Validate(as: 'mật khẩu mới')]
    public string $new_password = '';

    #[Validate(as: 'xác nhận mật khẩu')]
    public string $password_confirmation = '';


    public function render()
    {
        return view('livewire.profile.password');
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
            ],
            'new_password' => [
                'required',
                'min:8',
            ],
            'password_confirmation' => [
                'required',
            ]
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if (trim($this->password) !== trim($this->new_password)) {
            $this->addError('password_confirmation', 'Mật khẩu không trùng khớp');
            return;
        }

        if (!Hash::check($this->password, auth()->user()->password)) {
            $this->addError('password', 'Mật khẩu không chính xác');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);
        $this->dispatch('alert', type: 'success', message: 'Lưu thành công!');
        $this->clearForm();
    }

    public function clearForm(): void
    {
        $this->password = '';
        $this->password_confirmation = '';
        $this->new_password = '';
    }
}
