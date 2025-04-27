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

// Chuyển hướng từ route cũ sang route mới
Route::redirect('/login', '/login')->name('login');
Route::redirect('/logout', '/logout')->name('handleLogout');
Route::redirect('/handleLogin', '/login')->name('handleLogin');

Route::get('/authorize/azure', [AuthenticateController::class, 'redirectToSocialite'])->name('login.microsoft');
Route::get('/authorize/azure/callback', [AuthenticateController::class, 'handleSocialteCallback']);

//Route::middleware(['auth', 'check.password'])->group(function (): void {
//
//    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//    Route::prefix('/profile')->group(function (): void {
//        Route::get('/', [ProfileController::class, 'index'])->name('profile');
//    });
//
//    // Quản lý khoa
//    Route::prefix('/faculties')->group(function (): void {
//        Route::get('/', [FacultyController::class, 'index'])->name('faculty.index')->middleware('permission:faculty.view');
//        Route::get('/create', [FacultyController::class, 'create'])->name('faculty.create')->middleware('permission:faculty.create');
//        Route::get('/{faculty}', [FacultyController::class, 'show'])->name('faculty.show')->middleware('permission:faculty.view');
//        Route::get('/{faculty}/edit', [FacultyController::class, 'edit'])->name('faculty.edit')->middleware('permission:faculty.edit');
//    });
//
//    // Quản lý ứng dụng
//    Route::prefix('/clients')->group(function (): void {
//        Route::get('/', [ClientController::class, 'index'])->name('client.index')->middleware('permission:client.view');
//        Route::get('/create', [ClientController::class, 'create'])->name('client.create')->middleware('permission:client.create');
//        Route::get('/{client}', [ClientController::class, 'show'])->name('client.show')->middleware('permission:client.view');
//        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('client.edit')->middleware('permission:client.edit');
//    });
//
//    // Quản lý người dùng
//    Route::prefix('/users')->group(function (): void {
//        Route::get('/', [UserController::class, 'index'])->name('user.index')->middleware('permission:user.view');
//        Route::get('/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:user.create');
//        Route::get('/{user}', [UserController::class, 'show'])->name('user.show')->middleware('permission:user.view');
//        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:user.edit');
//        Route::get('/{user}/roles', [UserRoleController::class, 'edit'])->name('user.roles.edit')->middleware('permission:role.assign_users');
//    });
//
//    // Quản lý vai trò và phân quyền
//    Route::prefix('/roles')->group(function (): void {
//        Route::get('/', [RoleController::class, 'index'])->name('role.index')->middleware('permission:role.view');
//        Route::get('/create', [RoleController::class, 'create'])->name('role.create')->middleware('permission:role.create');
//        Route::get('/{role}', [RoleController::class, 'show'])->name('role.show')->middleware('permission:role.view');
//        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('role.edit')->middleware('permission:role.edit');
//    });
//});
