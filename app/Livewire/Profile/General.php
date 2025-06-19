<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class General extends Component
{
    #[Validate(as: 'username')]
    public string $user_name;
    #[Validate(as: 'first name')]
    public string $first_name;
    #[Validate(as: 'last name')]
    public string $last_name;
    #[Validate(as: 'email')]
    public string $email;
    #[Validate(as: 'phone')]
    public string|null $phone;

    public function mount(): void
    {
        $user = Auth::user();
        $this->user_name = $user->user_name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function rules(): array
    {
        return [
            'user_name' => 'required|unique:users,user_name,' . Auth::user()->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
            'phone' => 'nullable',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();
        $user->user_name = $this->user_name;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        if ($user->role !== Role::Student->value) {
            $user->email = $this->email;
        }
        $user->phone = $this->phone ?? '';
        $user->save();

        $this->dispatch('alert', type: 'success', message: 'Cập nhật thông tin thành công!');
    }

    public function render()
    {
        return view('livewire.profile.general');
    }
}
