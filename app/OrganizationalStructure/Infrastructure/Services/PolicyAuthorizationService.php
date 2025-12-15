<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Services;

use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Illuminate\Contracts\Auth\Access\Gate;
use Throwable;

/**
 * Policy Authorization Service Implementation.
 * Uses Laravel Gate internally but abstracts it from callers.
 *
 * SECURITY: Default deny - returns false on errors.
 */
final class PolicyAuthorizationService implements PolicyAuthorizationServiceInterface
{
    /**
     * @param Gate $gate Laravel Gate instance
     */
    public function __construct(
        private readonly Gate $gate,
    ) {
    }

    /**
     * Check if user can view any models of a given type.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param string $modelClass Model class name
     * @return bool True if user can view any, false otherwise
     */
    public function canViewAny(EloquentUser $user, string $modelClass): bool
    {
        try {
            return $this->gate->forUser($user)->allows('viewAny', $modelClass);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => 'viewAny',
                'model' => $modelClass,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user can view a specific model.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can view, false otherwise
     */
    public function canView(EloquentUser $user, object $model): bool
    {
        try {
            return $this->gate->forUser($user)->allows('view', $model);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => 'view',
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user can create models of a given type.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param string $modelClass Model class name
     * @return bool True if user can create, false otherwise
     */
    public function canCreate(EloquentUser $user, string $modelClass): bool
    {
        try {
            return $this->gate->forUser($user)->allows('create', $modelClass);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => 'create',
                'model' => $modelClass,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user can update a specific model.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can update, false otherwise
     */
    public function canUpdate(EloquentUser $user, object $model): bool
    {
        try {
            return $this->gate->forUser($user)->allows('update', $model);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => 'update',
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user can delete a specific model.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can delete, false otherwise
     */
    public function canDelete(EloquentUser $user, object $model): bool
    {
        try {
            return $this->gate->forUser($user)->allows('delete', $model);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => 'delete',
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user can perform a custom action on a model.
     *
     * SECURITY: Default deny on error.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @param string $action Action name (e.g., 'resetPassword', 'assignRole')
     * @return bool True if user can perform action, false otherwise
     */
    public function can(EloquentUser $user, object $model, string $action): bool
    {
        try {
            return $this->gate->forUser($user)->allows($action, $model);
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Policy authorization check failed', [
                'user_id' => $user->id,
                'action' => $action,
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
