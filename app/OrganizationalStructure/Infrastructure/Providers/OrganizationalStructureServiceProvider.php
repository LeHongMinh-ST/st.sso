<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Providers;

use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Infrastructure\Persistence\EloquentDepartmentRepository;
use App\OrganizationalStructure\Infrastructure\Persistence\EloquentFacultyRepository;
use App\OrganizationalStructure\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for OrganizationalStructure context.
 * Binds repository interfaces to implementations.
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
        // Bind repository interfaces to implementations
        $this->app->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(FacultyRepositoryInterface::class, EloquentFacultyRepository::class);
        $this->app->singleton(DepartmentRepositoryInterface::class, EloquentDepartmentRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {

    }
}
