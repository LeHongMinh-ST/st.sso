<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Policies;

use App\IdentityAccess\Application\Services\AuthorizationService;
use App\Models\Role;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Role policy.
 * Uses AuthorizationService from IdentityAccess Context for permission checks.
 */
class RolePolicy
{
    use HandlesAuthorization;

    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->authorizationService->can($user, 'role.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $this->authorizationService->can($user, 'role.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->authorizationService->can($user, 'role.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $this->authorizationService->can($user, 'role.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $this->authorizationService->can($user, 'role.delete');
    }
}
