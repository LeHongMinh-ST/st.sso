<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\PermissionServiceProvider::class,
    App\SharedKernel\Infrastructure\Providers\SharedKernelServiceProvider::class,
    App\OrganizationalStructure\Infrastructure\Providers\OrganizationalStructureServiceProvider::class,
];
