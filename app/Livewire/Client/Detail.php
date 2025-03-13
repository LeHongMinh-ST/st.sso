<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;

class Detail extends Component
{
    public $client;

    public function render()
    {
        return view('livewire.client.detail');
    }

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function delete()
    {
        $this->client->delete();
        $this->dispatch('notify', type: 'success', message: 'Client deleted successfully');
    }
}
