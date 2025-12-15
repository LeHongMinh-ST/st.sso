<?php

declare(strict_types=1);

namespace App\Policies;

use App\IdentityAccess\Application\Services\AuthorizationService;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Faculty policy.
 * Uses AuthorizationService from IdentityAccess Context for permission checks.
 */
class FacultyPolicy
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
        return $this->authorizationService->can($user, 'faculty.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Faculty $faculty): bool
    {
        return $this->authorizationService->can($user, 'faculty.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->authorizationService->can($user, 'faculty.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Faculty $faculty): bool
    {
        return $this->authorizationService->can($user, 'faculty.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Faculty $faculty): bool
    {
        return $this->authorizationService->can($user, 'faculty.delete');
    }
}
