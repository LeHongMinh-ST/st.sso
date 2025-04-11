<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public $client;

    public function render()
    {
        return view('livewire.client.detail');
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    #[On('deleteClient')]
    public function delete()
    {
        if (!auth()->user()->can('client.delete')) {
            session()->flash('error', 'Bạn không có quyền xóa ứng dụng!');
            return;
        }

        $this->client->delete();
        session()->flash('success', 'Xoá thành công ứng dụng!');
        return redirect()->route('client.index');
    }

    public function openDeleteModal(): void
    {
        if (!auth()->user()->can('client.delete')) {
            return;
        }

        $this->dispatch('onOpenDeleteModal');
    }
}
