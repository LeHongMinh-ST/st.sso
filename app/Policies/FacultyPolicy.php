<?php

declare(strict_types=1);

namespace App\Policies;

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
        return $user->hasPermissionTo('faculty.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Faculty $faculty): bool
    {
        return $user->hasPermissionTo('faculty.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('faculty.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Faculty $faculty): bool
    {
        return $user->hasPermissionTo('faculty.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Faculty $faculty): bool
    {
        return $user->hasPermissionTo('faculty.delete');
    }
}
