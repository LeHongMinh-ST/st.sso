<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Đặt session cho Filament
            session()->put('filament.sso.auth.id', $user->getAuthIdentifier());
            session()->put('filament.sso.auth.user', $user);
            session()->put('filament.sso.auth.remember', false);
        }

        return $next($request);
    }
}
