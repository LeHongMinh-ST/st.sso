<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class Settings extends Page
{
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Cài đặt';

    protected static ?string $title = 'Cài đặt hệ thống';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.settings';

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_logo' => config('app.logo', asset('assets/images/logoST.jpg')),
            'app_favicon' => config('app.favicon', asset('assets/images/logoST.jpg')),
            'app_description' => config('app.description', 'ST Single Sign-On'),
            'maintenance_mode' => config('app.maintenance_mode', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cài đặt chung')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Tên ứng dụng')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('app_url')
                            ->label('URL ứng dụng')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('app_logo')
                            ->label('Logo URL')
                            ->maxLength(255),
                        TextInput::make('app_favicon')
                            ->label('Favicon URL')
                            ->maxLength(255),
                        TextInput::make('app_description')
                            ->label('Mô tả ứng dụng')
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Cài đặt hệ thống')
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->label('Chế độ bảo trì')
                            ->helperText('Khi bật, người dùng không thể truy cập hệ thống trừ quản trị viên.')
                            ->default(false),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Lưu cài đặt vào cache hoặc database
        foreach ($data as $key => $value) {
            Cache::put("settings.{$key}", $value, now()->addYear());
        }

        $this->notify('success', 'Cài đặt đã được lưu thành công.');
    }
}
