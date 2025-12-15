<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Providers;

use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\Department;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\OrganizationalStructure\Infrastructure\Policies\DepartmentPolicy;
use App\OrganizationalStructure\Infrastructure\Policies\FacultyPolicy;
use App\OrganizationalStructure\Infrastructure\Policies\UserPolicy;
use App\OrganizationalStructure\Infrastructure\Services\PolicyAuthorizationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for OrganizationalStructure Context.
 * Registers services and bindings.
 */
final class OrganizationalStructureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Policy Authorization Service Interface to Implementation
        $this->app->singleton(
            PolicyAuthorizationServiceInterface::class,
            PolicyAuthorizationService::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register policies within the OrganizationalStructure context
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
    }
}
