<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Logs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Nhật ký hệ thống (Cũ)';

    protected static ?string $title = 'Nhật ký hệ thống';

    protected static ?string $slug = 'logs-old';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationGroup = 'Hệ thống';

    // Ẩn trang này khỏi menu
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        // Chuyển hướng đến trang ActivityLogResource
        redirect()->to(route('filament.sso.resources.logs.index'))->send();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.pages.redirect');
    }
}
