<?php

declare(strict_types=1);

namespace App\Http\Controllers\Filament\Auth;

use App\Http\Controllers\Controller;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the logout request.
     */
    public function __invoke(Request $request): LogoutResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
