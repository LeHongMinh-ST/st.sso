<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use App\Policies\PermissionPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the permission policy
        Gate::define('hasPermission', [PermissionPolicy::class, 'hasPermission']);
        Gate::define('hasAnyPermission', [PermissionPolicy::class, 'hasAnyPermission']);
        Gate::define('hasAllPermissions', [PermissionPolicy::class, 'hasAllPermissions']);

        // Register permissions as gates
        try {
            if ('testing' !== app()->environment() && !app()->runningInConsole()) {
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    Gate::define($permission->code, fn (User $user) => $user->hasPermission($permission->code));
                }
            }
        } catch (Exception $e) {
            // If the permissions table doesn't exist yet, we'll just skip this
            // This can happen during migrations
        }
    }
}
