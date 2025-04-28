<?php

declare(strict_types=1);

namespace App\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('filament.sso.auth.login');
    }
}
