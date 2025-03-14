<?php

declare(strict_types=1);

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

    public function mount(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }
}
