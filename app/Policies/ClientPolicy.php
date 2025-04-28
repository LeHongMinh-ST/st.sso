<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('client.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        // Quản trị viên có thể xem tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('client.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ quản trị viên mới có thể tạo ứng dụng mới
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('client.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        // Quản trị viên có thể cập nhật tất cả
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('client.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        // Chỉ quản trị viên mới có thể xóa ứng dụng
        if (RoleEnum::SuperAdmin === $user->role) {
            return true;
        }

        return $user->hasPermission('client.delete');
    }
}
