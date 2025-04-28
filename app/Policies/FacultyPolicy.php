<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FacultyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('faculty.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Faculty $faculty): bool
    {
        // Quản trị viên có thể xem tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể xem khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $faculty->id) {
            return true;
        }

        return $user->hasPermission('faculty.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ quản trị viên mới có thể tạo khoa mới
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('faculty.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Faculty $faculty): bool
    {
        // Quản trị viên có thể cập nhật tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể cập nhật khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $faculty->id) {
            return $user->hasPermission('faculty.edit');
        }

        return $user->hasPermission('faculty.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Faculty $faculty): bool
    {
        // Chỉ quản trị viên mới có thể xóa khoa
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('faculty.delete');
    }
}
