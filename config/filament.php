<?php

declare(strict_types=1);

return [
    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            //            \App\Filament\Widgets\ClientsOverview::class
            //            \App\Filament\Widgets\StatsOverview::class,
            //            \App\Filament\Widgets\WelcomeWidget::class,
        ],
    ],

    'auth' => [
        'guard' => 'web',
        'model' => App\Models\User::class,
    ],

    'middleware' => [
        'auth' => [
            'web',
        ],
    ],

    // Cấu hình ngôn ngữ
    'default_locale' => 'vi',
    'locale' => 'vi',
];
