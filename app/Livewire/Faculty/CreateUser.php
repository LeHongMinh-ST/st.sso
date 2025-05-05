<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class CreateUser extends Component
{
    public Faculty $faculty;

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

    public Role $role = Role::Normal;

    #[Validate(as: 'mã người dùng')]
    public string $code = '';

    private bool $isLoading = false;

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function render()
    {
        return view('livewire.faculty.create-user');
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:20',
        ];

        // Mã sinh viên là bắt buộc nếu loại tài khoản là sinh viên
        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code';
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code';
        } else {
            $rules['code'] = 'nullable|max:255';
        }

        return $rules;
    }

    public function mount(Faculty $faculty): void
    {
        $this->faculty = $faculty;
    }

    public function submit(): void
    {
        if ($this->isLoading) {
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
                'faculty_id' => $this->faculty->id,
                'status' => Status::Active->value,
            ]);

            // Gán vai trò cho người dùng mới
            switch ($this->role) {
                case Role::SuperAdmin:
                    $user->assignRole('super-admin');
                    break;
                case Role::Officer:
                    $user->assignRole('faculty-admin');
                    break;
                case Role::Teacher:
                    $user->assignRole('teacher');
                    break;
                case Role::Student:
                    $user->assignRole('student');
                    break;
                default:
                    $user->assignRole('normal');
                    break;
            }

            session()->flash('success', 'Tạo mới người dùng thành công!');
            $this->reset(['user_name', 'first_name', 'last_name', 'email', 'phone', 'code']);
            $this->role = Role::Normal;
            $this->dispatch('userCreated');
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
