<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        // Quản trị viên có thể xem tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('role.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ quản trị viên mới có thể tạo vai trò mới
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('role.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Quản trị viên có thể cập nhật tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('role.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Chỉ quản trị viên mới có thể xóa vai trò
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('role.delete');
    }
}
