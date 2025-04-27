<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Status;
use App\Models\Client;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $activeUsers = User::where('status', Status::Active)->count();
        $totalUsers = User::count();
        $userPercentage = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;

        $activeClients = Client::where(function ($query): void {
            $query->where('status', Status::Active)
                ->orWhereNull('status');
        })->count();
        $totalClients = Client::count();
        $clientPercentage = $totalClients > 0 ? round(($activeClients / $totalClients) * 100) : 0;

        return [
            Stat::make('Người dùng', number_format($totalUsers))
                ->description($activeUsers . ' đang hoạt động (' . $userPercentage . '%)')
                ->descriptionIcon('heroicon-m-users')
                ->chart([$activeUsers, $totalUsers - $activeUsers])
                ->color('primary'),

            Stat::make('Khoa & Bộ môn', number_format(Faculty::count()))
                ->description(number_format(Department::count()) . ' bộ môn')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Ứng dụng', number_format($totalClients))
                ->description($activeClients . ' đang hoạt động (' . $clientPercentage . '%)')
                ->descriptionIcon('heroicon-m-window')
                ->chart([$activeClients, $totalClients - $activeClients])
                ->color('warning'),
        ];
    }
}
