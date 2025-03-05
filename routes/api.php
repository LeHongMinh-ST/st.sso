<?php

use App\Http\Controllers\Api\OAuthClientController;
use Illuminate\Support\Facades\Route;

Route::get('/sso/login', [OAuthClientController::class, 'loginSSO']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('oauth/clients', OAuthClientController::class);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    });
});
