<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa ứng dụng')
                ->icon('heroicon-o-trash'),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();
            $this->callHook('afterValidate');

            $this->callHook('beforeSave');

            $this->client->update([
                'name' => $data['name'],
                'redirect' => $data['redirect'],
                'description' => $data['description'],
                'allowed_roles' => $data['allowed_roles'],
                'is_show_dashboard' => $data['is_show_dashboard'],
                'status' => $data['status'],
                'logo' => $data['logo'] ?? null,
            ]);

            $this->callHook('afterSave');
        } catch (\Exception $exception) {
            $this->handleRecordUpdateException($exception);

            return;
        }

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl);
        }
    }
}
