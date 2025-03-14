<?php

declare(strict_types=1);

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

    public string $id;

    public string $secret;

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

    public function submit(): void
    {
        $this->validate();

        $client = app(ClientRepository::class)->create(Auth::user()->id, $this->name, $this->redirect);
        $client->description = $this->description;
        $client->save();

        $this->id = $client->id;
        $this->secret = $client->secret;

        $this->openSuccessModal();
    }

    public function openSuccessModal(): void
    {
        $this->dispatch('open-success-modal');
    }
}
