<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\AuthenticateController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthenticateController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthenticateController::class, 'login'])->name('handleLogin');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');

Route::get('/authorize/azure', [AuthenticateController::class, 'redirectToSocialite'])->name('login.microsoft');
Route::get('/authorize/azure/callback', [AuthenticateController::class, 'handleSocialteCallback']);

Route::middleware(['auth', 'check.password'])->group(function (): void {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('/profile')->group(function (): void {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
    });

    Route::middleware(['check.superadmin'])->group(function (): void {
        Route::prefix('/faculties')->group(function (): void {
            Route::get('/', [FacultyController::class, 'index'])->name('faculty.index');
        });

        Route::prefix('/clients')->group(function (): void {
            Route::get('/', [ClientController::class, 'index'])->name('client.index');
            Route::get('/create', [ClientController::class, 'create'])->name('client.create');
            Route::get('/{client}', [ClientController::class, 'show'])->name('client.show');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('client.edit');
        });
    });
});
