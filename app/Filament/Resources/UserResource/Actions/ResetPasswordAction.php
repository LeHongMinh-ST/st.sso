<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Actions;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;

class ResetPasswordAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Đặt lại mật khẩu')
            ->icon('heroicon-o-key')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Đặt lại mật khẩu')
            ->modalDescription('Bạn có chắc chắn muốn đặt lại mật khẩu cho người dùng này? Mật khẩu mới sẽ được tạo ngẫu nhiên và hiển thị sau khi đặt lại.')
            ->modalSubmitActionLabel('Đặt lại mật khẩu')
            ->modalCancelActionLabel('Hủy')
            ->visible(fn (User $record): bool => auth()->user()->can('resetPassword', $record))
            ->action(function (User $user) {
                // Tạo mật khẩu ngẫu nhiên 8 số
                $newPassword = (string) rand(10000000, 99999999);
                
                // Cập nhật mật khẩu
                $user->password = Hash::make($newPassword);
                $user->is_change_password = false;
                $user->save();
                
                // Hiển thị thông báo với mật khẩu mới
                Notification::make()
                    ->title('Đặt lại mật khẩu thành công')
                    ->body("Mật khẩu mới của người dùng {$user->full_name} là: **{$newPassword}**")
                    ->success()
                    ->persistent()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('copy')
                            ->label('Sao chép mật khẩu')
                            ->icon('heroicon-o-clipboard')
                            ->button()
                            ->color('success')
                            ->extraAttributes([
                                'x-on:click' => "navigator.clipboard.writeText('{$newPassword}'); \$tooltip('Mật khẩu đã được sao chép')",
                            ]),
                    ])
                    ->send();
            });
    }
}
