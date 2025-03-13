<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;

class Edit extends Component
{
    public $client;

    public function render()
    {
        return view('livewire.client.edit');
    }

    public function mount(Client $client)
    {
        $this->client = $client;
    }
}
