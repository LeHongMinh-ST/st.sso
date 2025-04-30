<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    public function getTitle(): string
    {
        return 'Nhật ký hệ thống';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
