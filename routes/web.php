<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Middleware\CheckPasswordChanged;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthenticateController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthenticateController::class, 'login'])->name('handleLogin');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');

Route::get('/authorize/azure', [AuthenticateController::class, 'redirectToSocialite'])->name('login.microsoft');
Route::get('/authorize/azure/callback', [AuthenticateController::class, 'handleSocialteCallback']);

Route::middleware(['auth', 'check.password'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('/profile')->group(function () {
       Route::get('/', [ProfileController::class, 'index'])->name('profile');
    });
});
