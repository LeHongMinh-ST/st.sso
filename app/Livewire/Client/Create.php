<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate(as: 'tên client')]
    public string $name;

    #[Validate(as: 'redirect url')]
    public string $redirect;

    #[Validate(as: 'mô tả')]
    public string $description;

    public int $id;

    public string $secret;

    public function __construct(
        private ClientRepository $clientRepository
    )
    {
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'redirect' => 'required',
        ];
    }

    public function render()
    {
        return view('livewire.client.create');
    }

    public function submit()
    {
        $this->validate();
        
        $client = $this->clientRepository->create(Auth::user()->id, $this->name, $this->redirect, $this->description);

        $this->id = $client->id;
        $this->secret = $client->secret;

        $this->dispatch('notify', type: 'success', message: 'Client created successfully');
    }
}
