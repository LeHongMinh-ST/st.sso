<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Enums\Role;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class Create extends Component
{
    #[Validate(as: 'tên người dùng')]
    public string $user_name;

    #[Validate(as: 'tên')]
    public string $first_name;

    #[Validate(as: 'họ')]
    public string $last_name;

    #[Validate(as: 'email')]
    public string $email;

    #[Validate(as: 'số điện thoại')]
    public string $phone = '';

    public Role $role = Role::Officer;

    #[Validate(as: 'mã người dùng')]
    public string $code = '';

    public bool $is_only_login_ms = false;

    public int|null|string $department_id = null;

    public int|null|string $faculty_id = null;

    private bool $isLoading = false;

    public function render()
    {
        $faculties = Faculty::all();

        return view('livewire.user.create', [
            'faculties' => $faculties
        ]);
    }

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'department_id' => 'nullable',
            'faculty_id' => 'nullable',
            'is_only_login_ms' => 'nullable|boolean',
        ];

        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code';
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'nullable|max:255|unique:users,code';
        } else {
            $rules['code'] = 'nullable|max:255';
        }

        return $rules;
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('create', User::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền tạo người dùng!');
            return;
        }

        $this->validate();

        try {
            $this->isLoading = true;
            $user = User::create([
                'user_name' => $this->user_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make('password'),
                'phone' => $this->phone,
                'role' => $this->role->value,
                'code' => $this->code,
                'is_change_password' => false,
                'department_id' => $this->department_id,
                'faculty_id' => $this->faculty_id,
                'is_only_login_ms' => $this->is_only_login_ms,
            ]);

            session()->flash('success', 'Tạo mới thành công!');
            return redirect()->route('user.show', $user->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleIsOnlyLoginMs(): void
    {
        $this->is_only_login_ms = ! $this->is_only_login_ms;
    }
}
