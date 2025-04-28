<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('user.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Người dùng có thể xem thông tin của chính mình
        if ($user->id === $model->id) {
            return true;
        }

        // Quản trị viên có thể xem tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể xem người dùng trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $model->faculty_id) {
            return $user->hasPermission('user.view');
        }

        return $user->hasPermission('user.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('user.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Người dùng có thể cập nhật thông tin của chính mình
        if ($user->id === $model->id) {
            return true;
        }

        // Quản trị viên có thể cập nhật tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể cập nhật người dùng trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $model->faculty_id) {
            return $user->hasPermission('user.edit');
        }

        return $user->hasPermission('user.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Không thể xóa chính mình
        if ($user->id === $model->id) {
            return false;
        }

        // Quản trị viên có thể xóa tất cả (trừ chính mình)
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể xóa người dùng trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $model->faculty_id) {
            return $user->hasPermission('user.delete');
        }

        return $user->hasPermission('user.delete');
    }

    /**
     * Determine whether the user can reset password.
     */
    public function resetPassword(User $user, User $model): bool
    {
        // Quản trị viên có thể đặt lại mật khẩu cho tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể đặt lại mật khẩu cho người dùng trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $model->faculty_id) {
            return $user->hasPermission('user.reset_password');
        }

        return $user->hasPermission('user.reset_password');
    }
}
