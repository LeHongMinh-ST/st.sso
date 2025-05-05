<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserRoleController;
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

    // Quản lý khoa
    Route::prefix('/faculties')->group(function (): void {
        Route::get('/', [FacultyController::class, 'index'])->name('faculty.index')->middleware('can:viewAny,App\Models\Faculty');
        Route::get('/create', [FacultyController::class, 'create'])->name('faculty.create')->middleware('can:create,App\Models\Faculty');
        Route::get('/{faculty}', [FacultyController::class, 'show'])->name('faculty.show')->middleware('can:view,faculty');
        Route::get('/{faculty}/edit', [FacultyController::class, 'edit'])->name('faculty.edit')->middleware('can:update,faculty');
    });

    // Quản lý ứng dụng
    Route::prefix('/clients')->group(function (): void {
        Route::get('/', [ClientController::class, 'index'])->name('client.index')->middleware('can:viewAny,App\Models\Client');
        Route::get('/create', [ClientController::class, 'create'])->name('client.create')->middleware('can:create,App\Models\Client');
        Route::get('/{client}', [ClientController::class, 'show'])->name('client.show')->middleware('can:view,client');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('client.edit')->middleware('can:update,client');
    });

    // Quản lý người dùng
    Route::prefix('/users')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('user.index')->middleware('can:viewAny,App\Models\User');
        Route::get('/create', [UserController::class, 'create'])->name('user.create')->middleware('can:create,App\Models\User');
        Route::get('/{user}', [UserController::class, 'show'])->name('user.show')->middleware('can:view,user');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('can:update,user');
        Route::get('/{user}/roles', [UserRoleController::class, 'edit'])->name('user.roles.edit')->middleware('can:viewAny,App\Models\Role');
    });

    // Quản lý vai trò và phân quyền
    Route::prefix('/roles')->group(function (): void {
        Route::get('/', [RoleController::class, 'index'])->name('role.index')->middleware('can:viewAny,App\Models\Role');
        Route::get('/create', [RoleController::class, 'create'])->name('role.create')->middleware('can:create,App\Models\Role');
        Route::get('/{role}', [RoleController::class, 'show'])->name('role.show')->middleware('can:view,role');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('role.edit')->middleware('can:update,role');
    });
});
