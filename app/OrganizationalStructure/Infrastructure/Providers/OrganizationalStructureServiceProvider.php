<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Providers;

use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use App\OrganizationalStructure\Infrastructure\Services\PolicyAuthorizationService;
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

    }
}
