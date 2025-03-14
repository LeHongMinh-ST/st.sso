<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Client $client;

    #[Validate(as: 'tên client')]
    public string $name;
    #[Validate(as: 'redirect url')]
    public string $redirect;
    #[Validate(as: 'mô tả')]
    public string $description;

    public function render()
    {
        return view('livewire.client.edit');
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
        $this->name = $client->name;
        $this->redirect = $client->redirect;
        $this->description = $client->description;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'redirect' => 'required',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'redirect' => $this->redirect,
            'description' => $this->description,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Client updated successfully');
    }
}
