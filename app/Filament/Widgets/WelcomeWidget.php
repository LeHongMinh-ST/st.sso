<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';

    protected int | string | array $columnSpan = 'full';

    public function getUserName(): string
    {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        return $user->full_name ?: $user->user_name ?: $user->email ?: 'User';
    }

    public function getCurrentTime(): string
    {
        $hour = (int) now()->format('H');

        if ($hour >= 5 && $hour < 12) {
            return 'Chào buổi sáng';
        }
        if ($hour >= 12 && $hour < 18) {
            return 'Chào buổi chiều';
        }
        return 'Chào buổi tối';

    }
}
