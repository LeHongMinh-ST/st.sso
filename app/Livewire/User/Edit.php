<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class Edit extends Component
{
    public User $user;

    #[Validate(as: 'tên người dùng')]
    public string $user_name;

    #[Validate(as: 'tên')]
    public string $first_name;

    #[Validate(as: 'họ')]
    public string $last_name;

    #[Validate(as: 'email')]
    public string $email;

    #[Validate(as: 'số điện thoại')]
    public string|null $phone = '';

    public Role $role = Role::Normal;

    #[Validate(as: 'mã người dùng')]
    public string $code = '';

    public int|null|string $department_id = null;

    public int|null|string $faculty_id = null;

    public Status $status = Status::Active;

    private bool $isLoading = false;

    public function render()
    {
        $faculties = Faculty::all();

        return view('livewire.user.edit', [
            'faculties' => $faculties
        ]);
    }

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->user_name = $user->user_name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->role;
        $this->code = $user->code;
        $this->department_id = $user->department_id;
        $this->faculty_id = $user->faculty_id;
    }

    public function rules(): array
    {
        $rules = [
            'user_name' => 'required|max:255|unique:users,user_name,' . $this->user->id,
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'code' => 'nullable|max:255|unique:users,code,' . $this->user->id,
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
        ];

        if (Role::Student === $this->role) {
            $rules['code'] = 'required|max:255|unique:users,code,' . $this->user->id;
        } elseif (Role::Officer === $this->role) {
            $rules['code'] = 'nullable|max:255|unique:users,code,' . $this->user->id;
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

        if (!auth()->user()->can('update', $this->user)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền chỉnh sửa người dùng!');
            return;
        }

        $this->validate();
        try {
            $this->isLoading = true;

            $this->user->update([
                'user_name' => $this->user_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role->value,
                'code' => $this->code,
                'department_id' => $this->department_id,
                'faculty_id' => $this->faculty_id,
            ]);

            session()->flash('success', 'Cập nhật thành công!');
            return redirect()->route('user.show', $this->user->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }

    public function updatedRole(): void
    {
        // Reset code when role changes
        $this->code = '';
    }

    public function toggleStatus(): void
    {
        $this->status = Status::Active === $this->status
            ? Status::Inactive
            : Status::Active;
    }
}
