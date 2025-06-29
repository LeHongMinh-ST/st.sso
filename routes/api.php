<?php

declare(strict_types=1);

use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:api');
Route::middleware(['auth:api'])->group(function (): void {
    // User API
    Route::apiResource('users', UserController::class)
        ->only(['index', 'show', 'store']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword']);
});

Route::apiResource('faculties', FacultyController::class)
    ->middleware(['auth:api', 'check.superadmin.api'])->only(['index']);
Route::get('faculties/get-all', [FacultyController::class, 'all'])
    ->middleware(['auth:api']);
Route::get('faculties/{faculty}/users', [FacultyController::class, 'getUsers'])
    ->middleware(['auth:api']);
Route::get('faculties/{faculty}/teachers', [FacultyController::class, 'getTeachers'])
    ->middleware(['auth:api']);

// get department by faculty
Route::get('faculties/{faculty}/departments', [FacultyController::class, 'getDepartments'])
    ->middleware(['client.credentials']);
