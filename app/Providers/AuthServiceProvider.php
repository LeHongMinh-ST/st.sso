<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Client;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Role;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
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
