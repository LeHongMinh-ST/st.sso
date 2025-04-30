<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Filament\Auth\LoginController;
use App\Http\Controllers\Filament\Auth\LogoutController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

// Chuyển hướng từ route cũ sang route mới
Route::get('/login', [LoginController::class, 'create'])
    ->name('filament.sso.auth.login');

Route::post('/login', [LoginController::class, 'store'])
    ->name('filament.sso.auth.login.store');

Route::post('/logout', LogoutController::class)
    ->name('filament.sso.auth.logout');

Route::get('/authorize/azure', [AuthenticateController::class, 'redirectToSocialite'])->name('login.microsoft');
Route::get('/authorize/azure/callback', [AuthenticateController::class, 'handleSocialteCallback']);

// Templates download
Route::get('/templates/teachers', [TemplateController::class, 'downloadTeachersTemplate'])->name('templates.teachers');
Route::get('/templates/students', [TemplateController::class, 'downloadStudentsTemplate'])->name('templates.students');
