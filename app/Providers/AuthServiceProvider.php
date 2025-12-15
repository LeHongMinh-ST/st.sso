<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Client;
use App\Models\Role;
use App\OrganizationalStructure\Infrastructure\Eloquent\Department;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\OrganizationalStructure\Infrastructure\Policies\ClientPolicy;
use App\OrganizationalStructure\Infrastructure\Policies\DepartmentPolicy;
use App\OrganizationalStructure\Infrastructure\Policies\FacultyPolicy;
use App\OrganizationalStructure\Infrastructure\Policies\RolePolicy;
use App\OrganizationalStructure\Infrastructure\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Faculty::class => FacultyPolicy::class,
        Department::class => DepartmentPolicy::class,
        Client::class => ClientPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
