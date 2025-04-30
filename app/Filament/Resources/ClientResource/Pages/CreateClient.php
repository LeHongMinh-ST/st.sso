<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();
            $this->callHook('afterValidate');

            $this->callHook('beforeCreate');

            $client = app(ClientRepository::class)
                ->create(Auth::user()->id, $data['name'], $data['redirect']);

            Client::where('id', $client->id)->update([
                'description' => $data['description'] ?? null,
                'allowed_roles' => $data['allowed_roles'] ?? [],
                'is_show_dashboard' => $data['is_show_dashboard'] ?? true,
                'status' => $data['status'] ?? null,
                'logo' => $data['logo'] ?? null,
            ]);

            $this->record = $client;

            $this->callHook('afterCreate');
        } catch (Exception $exception) {
            $this->handleRecordCreationException($exception);

            return;
        }

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->record::class);
            $this->record = null;
            $this->fillForm();

            return;
        }

        $this->redirect($this->getRedirectUrl());
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['personal_access_client'] = false;
        $data['password_client'] = false;
        $data['revoked'] = false;

        return $data;
    }
}
