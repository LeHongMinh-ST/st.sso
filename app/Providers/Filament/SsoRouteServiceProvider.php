<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Controllers\Filament\Auth\LoginController;
use App\Http\Controllers\Filament\Auth\LogoutController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class SsoRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //        $this->routes(function (): void {
        //            Route::middleware('web')
        //                ->group(function (): void {
        //                    Route::get('/login', [LoginController::class, 'create'])
        //                        ->name('filament.sso.auth.login');
        //
        //                    Route::post('/login', [LoginController::class, 'store'])
        //                        ->name('filament.sso.auth.login.store');
        //
        //                    Route::post('/logout', LogoutController::class)
        //                        ->name('filament.sso.auth.logout');
        //                });
        //        });
    }
}
