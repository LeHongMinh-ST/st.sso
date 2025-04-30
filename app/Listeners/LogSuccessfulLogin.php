<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        ActivityLogService::logLogin($event->user);
    }
}
