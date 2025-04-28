<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Kiểm tra xem có tham số activeTab trong URL không
        if (request()->has('activeTab')) {
            $activeTab = request()->get('activeTab');

            // Nếu activeTab là 'phân-quyền', đặt tab là 'Phân quyền'
            if ('phân-quyền' === $activeTab) {
                $this->js("setTimeout(() => {
                    const tabButton = document.querySelector('.filament-profile-tabs .fi-tabs-item:nth-child(2)');
                    if (tabButton) tabButton.click();
                }, 100)");
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa người dùng')
                ->icon('heroicon-o-trash'),
        ];
    }
}
