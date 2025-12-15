<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\Services;

use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;

/**
 * Policy Authorization Service Interface.
 * Abstracts policy checks from Laravel framework.
 *
 * This interface allows different contexts to check authorization
 * without depending on Laravel's authorization system directly.
 */
interface PolicyAuthorizationServiceInterface
{
    /**
     * Check if user can view any models of a given type.
     *
     * @param EloquentUser $user User to check
     * @param string $modelClass Model class name (e.g., User::class, Faculty::class)
     * @return bool True if user can view any, false otherwise
     */
    public function canViewAny(EloquentUser $user, string $modelClass): bool;

    /**
     * Check if user can view a specific model.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can view, false otherwise
     */
    public function canView(EloquentUser $user, object $model): bool;

    /**
     * Check if user can create models of a given type.
     *
     * @param EloquentUser $user User to check
     * @param string $modelClass Model class name
     * @return bool True if user can create, false otherwise
     */
    public function canCreate(EloquentUser $user, string $modelClass): bool;

    /**
     * Check if user can update a specific model.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can update, false otherwise
     */
    public function canUpdate(EloquentUser $user, object $model): bool;

    /**
     * Check if user can delete a specific model.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @return bool True if user can delete, false otherwise
     */
    public function canDelete(EloquentUser $user, object $model): bool;

    /**
     * Check if user can perform a custom action on a model.
     *
     * @param EloquentUser $user User to check
     * @param object $model Model instance to check
     * @param string $action Action name (e.g., 'resetPassword', 'assignRole')
     * @return bool True if user can perform action, false otherwise
     */
    public function can(EloquentUser $user, object $model, string $action): bool;
}
