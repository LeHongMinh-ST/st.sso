<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});
Route::post('/authorize/azure', [AuthenticatedSessionController::class, 'redirectToSocialite'])->name('login.microsoft');
Route::get('/authorize/azure/callback', [AuthenticatedSessionController::class, 'handleSocialteCallback']);

require __DIR__.'/settings.php';
require __DIR__.'/faculty.php';
require __DIR__.'/auth.php';
