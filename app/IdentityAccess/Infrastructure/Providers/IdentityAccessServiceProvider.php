<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Providers;

use App\IdentityAccess\Domain\Repositories\AccessTokenRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\ClientRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\Services\TokenGeneratorInterface;
use App\IdentityAccess\Infrastructure\Persistence\EloquentAccessTokenRepository;
use App\IdentityAccess\Infrastructure\Persistence\EloquentClientRepository;
use App\IdentityAccess\Infrastructure\Persistence\EloquentPermissionRepository;
use App\IdentityAccess\Infrastructure\Persistence\EloquentRoleRepository;
use App\IdentityAccess\Infrastructure\Persistence\EloquentUserIdentityRepository;
use App\IdentityAccess\Infrastructure\Policies\ClientPolicy;
use App\IdentityAccess\Infrastructure\Policies\RolePolicy;
use App\IdentityAccess\Infrastructure\Services\LaravelPasswordHasher;
use App\IdentityAccess\Infrastructure\Services\PassportTokenGenerator;
use App\IdentityAccess\Infrastructure\Services\UserIdentityBridgeService;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for IdentityAccess Context.
 * Registers repository and service bindings.
 */
final class IdentityAccessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(
            UserIdentityRepositoryInterface::class,
            EloquentUserIdentityRepository::class,
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            EloquentRoleRepository::class,
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            EloquentPermissionRepository::class,
        );

        $this->app->bind(
            ClientRepositoryInterface::class,
            EloquentClientRepository::class,
        );

        $this->app->bind(
            AccessTokenRepositoryInterface::class,
            EloquentAccessTokenRepository::class,
        );

        // Bind Domain Services Interfaces to Implementations
        $this->app->singleton(
            PasswordHasherInterface::class,
            LaravelPasswordHasher::class,
        );

        $this->app->singleton(
            TokenGeneratorInterface::class,
            PassportTokenGenerator::class,
        );

        // Bridge service (temporary - will be removed in Phase 6)
        $this->app->singleton(
            UserIdentityBridgeService::class,
            UserIdentityBridgeService::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
    }
}
