<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    public function getTitle(): string
    {
        $userName = 'Người dùng';
        if ($this->record->user) {
            $userName = $this->record->user->full_name;
        }
        return "Hoạt động của {$userName}";
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
