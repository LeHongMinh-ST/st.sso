<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;

class Detail extends Component
{
    public $client;

    protected $listeners = [
        'deleteClient' => 'delete',
    ];

    public function render()
    {
        return view('livewire.client.detail');
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function delete()
    {
        $this->client->delete();
        session()->flash('success', 'Xoá thành công ứng dụng!');
        return redirect()->route('client.index');
    }

    public function openDeleteModal(): void
    {
        $this->dispatch('onOpenDeleteModal');
    }
}
