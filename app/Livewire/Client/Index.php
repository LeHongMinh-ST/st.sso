<?php

namespace App\Livewire\Client;

use App\Helpers\Constants;
use App\Models\Client;
use Livewire\Component;

class Index extends Component
{

    public string $search = '';

    public function render()
    {
        $clients = Client::query()
            ->search($this->search)
            ->paginate(Constants::PER_PAGE_ADMIN);
        return view('livewire.client.index')->with([
            'clients' => $clients
        ]);
    }
}
