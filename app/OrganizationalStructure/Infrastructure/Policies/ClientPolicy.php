<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Policies;

use App\IdentityAccess\Application\Services\AuthorizationService;
use App\Models\Client;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Client policy.
 * Uses AuthorizationService from IdentityAccess Context for permission checks.
 */
class ClientPolicy
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
        return $this->authorizationService->can($user, 'client.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        return $this->authorizationService->can($user, 'client.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->authorizationService->can($user, 'client.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        return $this->authorizationService->can($user, 'client.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return $this->authorizationService->can($user, 'client.delete');
    }
}
