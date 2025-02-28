<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OAuthClientController;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('oauth/clients', OAuthClientController::class);
});