<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = Cache::get('settings.maintenance_mode', false);

        if ($maintenanceMode && auth()->check() && Role::SuperAdmin !== auth()->user()->role) {
            auth()->logout();

            return redirect()->route('filament.sso.auth.login')->with('error', 'Hệ thống đang trong chế độ bảo trì. Vui lòng quay lại sau.');
        }

        return $next($request);
    }
}
