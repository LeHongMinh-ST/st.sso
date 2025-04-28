<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    use InteractsWithForms;

    public ?array $personalData = [];

    public ?array $passwordData = [];

    // Biến để kiểm soát tab đang hiển thị
    public string $activeTab = 'personal';

    // Biến để kiểm tra xem người dùng đã đổi mật khẩu chưa
    public bool $isPasswordChanged = true;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Hồ sơ';

    protected static ?string $title = 'Hồ sơ cá nhân';

    protected static ?string $slug = 'profile';

    protected static ?int $navigationSort = 99;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.profile';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user) {
            // Lấy thông tin người dùng từ session Filament
            $filamentUser = session()->get('filament.sso.auth.user');
            $currentUser = $filamentUser ?? $user;

            // Kiểm tra xem người dùng đã đổi mật khẩu chưa
            $this->isPasswordChanged = $currentUser->is_change_password;

            // Nếu chưa đổi mật khẩu, mặc định hiển thị tab đổi mật khẩu
            if (! $this->isPasswordChanged) {
                $this->activeTab = 'password';
            }

            // Điền thông tin cá nhân
            $this->personalForm->fill([
                'first_name' => $currentUser->first_name,
                'last_name' => $currentUser->last_name,
                'email' => $currentUser->email,
                'phone' => $currentUser->phone,
            ]);
        }
    }

    // Chuyển tab
    public function switchTab(string $tab): void
    {
        // Nếu chưa đổi mật khẩu, chỉ cho phép hiển thị tab đổi mật khẩu
        if (! $this->isPasswordChanged && 'password' !== $tab) {
            Notification::make()
                ->title('Cảnh báo')
                ->body('Bạn cần đổi mật khẩu trước khi truy cập các tính năng khác.')
                ->warning()
                ->send();

            return;
        }

        $this->activeTab = $tab;
    }

    public function submitPersonalInfo(): void
    {
        // Nếu chưa đổi mật khẩu, không cho phép cập nhật thông tin cá nhân
        if (! $this->isPasswordChanged) {
            Notification::make()
                ->title('Cảnh báo')
                ->body('Bạn cần đổi mật khẩu trước khi cập nhật thông tin cá nhân.')
                ->warning()
                ->send();

            return;
        }

        $data = $this->personalForm->getState();

        // Lấy người dùng từ session hoặc auth
        $user = session()->get('filament.sso.auth.user') ?? auth()->user();

        if (! $user) {
            Notification::make()
                ->title('Lỗi')
                ->body('Không thể xác định người dùng đang đăng nhập.')
                ->danger()
                ->send();

            return;
        }

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        // Cập nhật lại session sau khi cập nhật thông tin
        session()->put('filament.sso.auth.user', $user->fresh());

        Notification::make()
            ->title('Thành công')
            ->body('Thông tin cá nhân đã được cập nhật thành công.')
            ->success()
            ->send();

        // Cập nhật lại form với dữ liệu mới
        $this->mount();
    }

    public function submitPasswordChange(): void
    {
        $data = $this->passwordForm->getState();

        // Lấy người dùng từ session hoặc auth
        $user = session()->get('filament.sso.auth.user') ?? auth()->user();

        if (! $user) {
            Notification::make()
                ->title('Lỗi')
                ->body('Không thể xác định người dùng đang đăng nhập.')
                ->danger()
                ->send();

            return;
        }

        if (! Hash::check($data['current_password'], $user->password)) {
            $this->addError('passwordData.current_password', 'Mật khẩu hiện tại không đúng.');

            return;
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
            'is_change_password' => true,
        ]);

        // Cập nhật lại session sau khi đổi mật khẩu
        session()->put('filament.sso.auth.user', $user->fresh());

        // Cập nhật trạng thái đã đổi mật khẩu
        $this->isPasswordChanged = true;

        // Xóa dữ liệu form mật khẩu
        $this->passwordData = [];

        Notification::make()
            ->title('Thành công')
            ->body('Mật khẩu đã được cập nhật thành công.')
            ->success()
            ->send();

        // Chuyển sang tab thông tin cá nhân sau khi đổi mật khẩu thành công
        $this->activeTab = 'personal';
    }

    protected function getForms(): array
    {
        return [
            'personalForm' => $this->makeForm()
                ->schema($this->getPersonalFormSchema())
                ->statePath('personalData'),
            'passwordForm' => $this->makeForm()
                ->schema($this->getPasswordFormSchema())
                ->statePath('passwordData'),
        ];
    }

    protected function getPersonalFormSchema(): array
    {
        return [
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
        ];
    }

    protected function getPasswordFormSchema(): array
    {
        return [
            Section::make('Đổi mật khẩu')
                ->schema([
                    TextInput::make('current_password')
                        ->label('Mật khẩu hiện tại')
                        ->password()
                        ->required(),
                    TextInput::make('new_password')
                        ->label('Mật khẩu mới')
                        ->password()
                        ->required(),
                    TextInput::make('new_password_confirmation')
                        ->label('Xác nhận mật khẩu mới')
                        ->password()
                        ->required()
                        ->same('new_password'),
                ])->columns(2),
        ];
    }
}
