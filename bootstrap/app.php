<?php

declare(strict_types=1);

use App\Http\Middleware\CheckApiPermission;
use App\Http\Middleware\CheckPasswordChanged;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Middleware\CheckSuperAdminApi;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.password' => CheckPasswordChanged::class,
            'check.superadmin' => CheckSuperAdmin::class,
            'check.superadmin.api' => CheckSuperAdminApi::class,
            'permission' => CheckPermission::class,
            'role' => CheckRole::class,
            'api.permission' => CheckApiPermission::class,
            'client.credentials' => CheckClientCredentials::class,
        ])->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {

    })->create();
