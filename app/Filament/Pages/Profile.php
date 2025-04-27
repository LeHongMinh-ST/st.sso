<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Hồ sơ';

    protected static ?string $title = 'Hồ sơ cá nhân';

    protected static ?string $slug = 'profile';

    protected static ?int $navigationSort = 99;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.profile';

    public function mount(): void
    {
        $this->form->fill([
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
            'email' => auth()->user()->email,
            'phone' => auth()->user()->phone,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin cá nhân')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Họ')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Đổi mật khẩu')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Mật khẩu hiện tại')
                            ->password()
                            ->dehydrated(false),
                        TextInput::make('new_password')
                            ->label('Mật khẩu mới')
                            ->password()
                            ->dehydrated(false),
                        TextInput::make('new_password_confirmation')
                            ->label('Xác nhận mật khẩu mới')
                            ->password()
                            ->dehydrated(false)
                            ->same('new_password'),
                    ])->columns(2),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (filled($data['current_password']) && filled($data['new_password'])) {
            if (! Hash::check($data['current_password'], $user->password)) {
                $this->addError('current_password', 'Mật khẩu hiện tại không đúng.');
                return;
            }

            $user->update([
                'password' => Hash::make($data['new_password']),
                'is_change_password' => true,
            ]);
        }

        $this->notify('success', 'Hồ sơ đã được cập nhật thành công.');
    }
}
