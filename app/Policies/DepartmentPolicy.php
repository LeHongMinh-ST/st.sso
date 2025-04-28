<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('department.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        // Quản trị viên có thể xem tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể xem bộ môn trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $department->faculty_id) {
            return true;
        }

        return $user->hasPermission('department.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Quản trị viên có thể tạo bộ môn mới
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa có thể tạo bộ môn trong khoa của mình
        if (RoleEnum::Officer === $user->role) {
            return $user->hasPermission('department.create');
        }

        return $user->hasPermission('department.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $department): bool
    {
        // Quản trị viên có thể cập nhật tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể cập nhật bộ môn trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $department->faculty_id) {
            return $user->hasPermission('department.edit');
        }

        return $user->hasPermission('department.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        // Quản trị viên có thể xóa tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        // Cán bộ khoa chỉ có thể xóa bộ môn trong khoa của mình
        if (RoleEnum::Officer === $user->role && $user->faculty_id === $department->faculty_id) {
            return $user->hasPermission('department.delete');
        }

        return $user->hasPermission('department.delete');
    }
}
