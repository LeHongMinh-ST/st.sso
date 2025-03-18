<?php

declare(strict_types=1);

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FacultyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:api');
Route::apiResource('user', UserController::class)
    ->only(['index', 'show'])
    ->middleware(['auth:api', 'check.superadmin']);

Route::apiResource('faculty', FacultyController::class)
    ->only(['index'])
    ->middleware(['auth:api', 'check.superadmin']);