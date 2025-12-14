<?php

declare(strict_types=1);

use App\Http\Controllers\Api\FacultyController as OldFacultyController;
use App\Http\Controllers\Api\UserController as OldUserController;
use App\OrganizationalStructure\Infrastructure\Http\Controllers\DepartmentController;
use App\OrganizationalStructure\Infrastructure\Http\Controllers\FacultyController;
use App\OrganizationalStructure\Infrastructure\Http\Controllers\UserController;
use App\SharedKernel\Infrastructure\Http\Controllers\HealthCheckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check endpoints (no authentication required)
Route::get('/health', [HealthCheckController::class, 'basic']);
Route::get('/health/detailed', [HealthCheckController::class, 'detailed']);

// Current user endpoint
Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:api');

// OrganizationalStructure Context - DDD API Routes
Route::middleware(['auth:api'])->group(function (): void {
    // User aggregate routes
    Route::apiResource('users', UserController::class);

    // Faculty aggregate routes
    Route::apiResource('faculties', FacultyController::class);

    // Department aggregate routes
    Route::apiResource('departments', DepartmentController::class);

    // Nested resources (only when there's a clear aggregate relationship)
    Route::get('faculties/{faculty}/users', [FacultyController::class, 'users'])
        ->name('faculties.users.index');
    Route::get('faculties/{faculty}/departments', [FacultyController::class, 'departments'])
        ->name('faculties.departments.index');
});

// Legacy routes (to be deprecated)
Route::middleware(['auth:api'])->group(function (): void {
    Route::apiResource('legacy-users', OldUserController::class)
        ->only(['index', 'show', 'store']);
    Route::post('legacy-users/{user}/reset-password', [OldUserController::class, 'resetPassword']);
});

Route::apiResource('legacy-faculties', OldFacultyController::class)
    ->middleware(['auth:api', 'check.superadmin.api'])->only(['index']);
Route::get('legacy-faculties/get-all', [OldFacultyController::class, 'all'])
    ->middleware(['auth:api']);
Route::get('legacy-faculties/{faculty}/users', [OldFacultyController::class, 'getUsers'])
    ->middleware(['auth:api']);
Route::get('legacy-faculties/{faculty}/teachers', [OldFacultyController::class, 'getTeachers'])
    ->middleware(['auth:api']);

// Legacy: get department by faculty
Route::get('legacy-faculties/{faculty}/departments', [OldFacultyController::class, 'getDepartments'])
    ->middleware(['client.credentials']);
