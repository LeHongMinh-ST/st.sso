<?php

namespace App\Livewire\Client\Modal;

use Livewire\Component;

class CreateSuccess extends Component
{
    public string $clientId;

    public string $clientSecret;

    public function render()
    {
        return view('livewire.client.modal.create-success');
    }

    public function mount(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }
}
